<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/3
 * Time: 9:18
 */

namespace app\index\controller;


use app\common\Base;
use think\Controller;
use app\index\model\User as UserModel;
use app\index\model\DocUserInfo;
use think\Config;
use think\Db;
use think\Request;
use think\Csv;

class Referrer extends Base
{
    private $redis;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->redis=new \Redis();
        $this->redis->connect(Config::get('redis.host'),Config::get('redis.port'));
        $this->redis->select(Config::get('redis.db_index'));
    }

    public function index()
    {

        //php获取上周起始时间戳和结束时间戳

        //$beginLastweek=mktime(0,0,0,date('m'),date('d')-date('w')+1-7,date('Y'));

      //  $endLastweek=mktime(23,59,59,date('m'),date('d')-date('w')+7-7,date('Y'));
        // return view('/referrer', ['data' => $this->getDataList()]);
       // dump($this->getDataList());
        
        $user = $this->getReferrer();
        //获取下级
        $data = $this->make_tree($user, $pk = 'id', $pid = 'parent_id', $child = '_child', $root = session('userid'));
        // dump($data);exit;
        $this->redis->set('referrer_data',serialize($data));
        return view('/referrer', ['data' => $data]);

    }

    public function getReferrer($where=[]){
        //获取推广下级
        $level = Config::get('level');
        //获取要展示的层级
        $c_num = 1;
        //设置当前的层级
        $data = array();
        //结果集数组
        $uid = array(session('userid'));
        //初始第一层级的userid
        $temp_uid = array();
        //临时记录uid的数组
        $temp_data = array();
        //临时记录数据的数组
        while ($c_num <= $level) {
            foreach ($uid as $k => $v) {
                //按照uid去查询下级
                $temp_data = UserModel::alias('a')
                            ->join('sales_user_info b','a.id = b.user_id','left')
                            ->join('sales_channel c','a.channel_id = c.id','left')
                            ->field('a.id,a.parent_id,a.user_name,a.create_time,b.first_name,b.last_name,c.channel_name')
                            ->where('a.parent_id',$v)
                            ->where($where)
                            ->select()
                            ->toArray();
                // dump($temp_data);exit;
                //查询结束后从uid集合里面删除这个uid
                if ($temp_data) {
                    foreach ($temp_data as $a => $b) {
                        array_push($temp_uid, $b['id']);
                        //得到新的uid存入临时uid集合数组中
                        array_push($data, $b);
                        //得到的新数据存入集合
                        $where = '1=1';
                        //清空查询条件
                    }

                }

            }

            $uid = $temp_uid;
            $temp_uid = array();
            // dump($uid);
            //刷新用户id用于查询下级  
            $c_num++;
        }

        // dump($data);exit;
        return $data;

    }

    public function getDataList($where=[])
    {
        $userList = Db::name('user')->field('id,user_name,parent_id')->select();
        $result = $this->GetTeamMember($userList, session('userid'));
        $data = [];
        foreach ($result as $k => $v) {
            $temp = Db::name('user')
                ->alias('a')
                ->join('user_info b', 'a.id=b.user_id','left')
                ->join('channel c', 'a.channel_id=c.id','left')
                ->field('a.user_name,a.create_time,b.first_name,b.last_name,c.channel_name')
                ->where('a.id', $v)
                ->where($where)
                //->whereTime('a.create_time', 'week')//显示一周之内的
                ->select();
            if($temp){
                $data[] = $temp;
            }
        }
        //return Db::name('user')->getLastSql();
        return $data;
    }

    public function make_tree($list, $pk = 'id', $pid = 'parent_id', $child = '_child', $root = 0)
    {
        $tree = [];
        $packData = [];
        foreach ($list as $data) {
            $packData[$data[$pk]] = $data;
        }
        foreach ($packData as $key => $val) {
            if ($val[$pid] == $root) {//代表跟节点
                $tree[] =& $packData[$key];
            } else {
                //找到其父类
                $packData[$val[$pid]][$child][] =& $packData[$key];
            }
        }
        return $tree;
    }

    public function findChildren(&$data, $parent_id = 0)
    {
        $rootList = [];
        foreach ($data as $key => $val) {
            if ($val['id'] == $parent_id) {
                $rootList[] = $val;
                unset($data[$key]);
            }
        }
        return $rootList;
    }

    public function testReferrerList()
    {
        //1.整个会员表的数据
        $member = array(
            array('id' => 1, 'parent_id' => 0, 'nickname' => 'A'),
            array('id' => 2, 'parent_id' => 1, 'nickname' => 'B'),
            array('id' => 3, 'parent_id' => 1, 'nickname' => 'C'),
            array('id' => 4, 'parent_id' => 8, 'nickname' => 'D'),
            array('id' => 5, 'parent_id' => 3, 'nickname' => 'E'),
            array('id' => 6, 'parent_id' => 3, 'nickname' => 'F'),
            array('id' => 7, 'parent_id' => 3, 'nickname' => 'G'),
            array('id' => 8, 'parent_id' => 8, 'nickname' => 'H')
        );
        /*
        *2.获取某个会员的无限下级方法
        *$members是所有会员数据表,$mid是用户的id
        */
        $res = $this->GetTeamMember($member, 1);
        var_dump($res);
    }

    public function GetTeamMember($members, $mid)
    {
        $Teams = [];//最终结果
        $mids = array($mid);//第一次执行时候的用户id
        do {
            $othermids = [];
            $state = false;
            foreach ($mids as $valueone) {
                foreach ($members as $key => $valuetwo) {
                    if ($valuetwo['parent_id'] == $valueone) {
                        $Teams[] = $valuetwo['id'];//找到我的下级立即添加到最终结果中
                        $othermids[] = $valuetwo['id'];//将我的下级id保存起来用来下轮循环他的下级
                        // array_splice($members, $key, 1);//从所有会员中删除他
                        unset($members[$key]);
                        $state = true;
                    }
                }
            }
            $mids = $othermids;//foreach中找到的我的下级集合,用来下次循环
        } while ($state == true);
        return $Teams;
    }

    public function search()
    {
        if (isset($_POST)) {
            $where = [];
            if (input('first_name')) {
                $where['b.first_name'] = input('first_name');
            }
            if (input('last_name')) {
                $where['b.last_name'] = input('last_name');
            }
            if (input('username')) {
                $where['a.user_name'] = input('username');
            }
            if (input('channel_name')) {
                $where['c.channel_name'] = input('channel_name');
            }

            // return json($this->getDataList($where));
            $user = $this->getReferrer($where);
            $data = $this->make_tree($user, $pk = 'id', $pid = 'parent_id', $child = '_child', $root = session('userid'));
            $this->redis->set('referrer_data',serialize($data));
            return view('/referrer', ['data' => $data]);
            // return json(['data'=>$data]);
        }
    }

    public function export(){
        //导出csv
        $csv = new Csv();

        $data = unserialize($this->redis->get('referrer_data'));
        //源数据
        $new_data = array();
        //新数据
        foreach ($data as $k => $val) {
            $list[0] = $val;
            $c_fix = '';
            while(!empty($list))
            {
                $one = array_shift($list);
                    // dump($one);exit;
                    $one['user_name'] = $c_fix.$one['user_name'];
                    $new_data[] = $one;
                    // dump($one);
                    if(isset($one['_child'])){
                    $list = array_merge($list,$one['_child']);
                }
                $c_fix.= '__';
            }
        }
        
        // dump($new_data);exit;
        $title = array('id',\think\lang::get('channel_name'),\think\lang::get('first_name'),\think\lang::get('last_name'),\think\lang::get('username'),\think\lang::get('signup_date'));
        //标题栏
        $field = array('id','channel_name','first_name','last_name','user_name','create_time');
        $file_name = 'Sales_Network';

        $csv->put_csv($new_data,$title,$field,$file_name);

    }

    public function changeRole(){
        //改变角色
        $role = input('post.role');
        if ($role == 0) {
            //销售
            $user = $this->getReferrer();
            //获取下级
            $data = $this->make_tree($user, $pk = 'id', $pid = 'parent_id', $child = '_child', $root = session('userid'));
            // dump($data);exit;
            $this->redis->set('referrer_data',serialize($data));
            return json(['data' => $data]);
        }
        if ($role == 1) {
            //医生
            $userid = session('userid');
            $doc = DocUserInfo::alias('a')
                               ->join('sales_channel b','a.channelId=b.id','left')
                               ->field(['firstName' => 'first_name','lastName' => 'last_name','contactPhone' => 'phone','a.user_id' => 'user_name','a.create_time' => 'create_time','channel_name' => 'channel_name'])
                               ->select();
            $this->redis->set('referrer_data',serialize($doc));
            return json(['data' => $doc]);
        }
        
    }
}