<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/3
 * Time: 9:18
 */

namespace app\index\controller;


use think\Controller;
use think\Db;

class Referrer extends Controller
{
    public function index()
    {
       // return view('/referrer');
        $data = Db::name('user')->field('id,user_name,parent_id')->order('parent_id', 'asc')->select();
        $result=$this->GetTeamMember($data,session('userid'));
        $data=[];
        foreach ($result as $k=>$v){
            $data[]= Db::name('user')
                ->alias('a')
                ->join('user_info b','a.id=b.user_id')
                ->join('channel c','a.channel_id=c.id')
                ->field('a.user_name,a.create_time,b.first_name,b.last_name,c.channel_name')
                ->where('a.id',$v)
                ->whereTime('a.create_time','week') //显示一周之内的
                ->select();
        }
        return view('/referrer', ['data' => $data]);
    }

    function make_tree($list,$pk='id',$pid='parent_id',$child='_child',$root=0){
        $tree=[];
        $packData=[];
        foreach ($list as  $data) {
            $packData[$data[$pk]] = $data;
        }
        foreach ($packData as $key =>$val){
            if($val[$pid]==$root){//代表跟节点
                $tree[]=& $packData[$key];
            }else{
                //找到其父类
                $packData[$val[$pid]][$child][]=& $packData[$key];
            }
        }
        return $tree;
    }
    public function findChildren(&$data,$parent_id=0)
    {
        foreach ($data as $key=>$val)
        {
            $rootList = array();
            foreach ($data as $key => $val) {
                if ($val['id'] == $parent_id) {
                    $rootList[]   = $val;
                    unset($data[$key]);
                }
//                else{
//                    $this->findChildren($val,$parent_id);
//                }
            }
            return $rootList;
        }
    }

    public function testReferrerList()
    {
        //1.整个会员表的数据
        $member = array(
            array('id'=>1, 'parent_id'=>0, 'nickname' => 'A'),
            array('id'=>2, 'parent_id'=>1, 'nickname' => 'B'),
            array('id'=>3, 'parent_id'=>1, 'nickname' => 'C'),
            array('id'=>4, 'parent_id'=>8, 'nickname' => 'D'),
            array('id'=>5, 'parent_id'=>3, 'nickname' => 'E'),
            array('id'=>6, 'parent_id'=>3, 'nickname' => 'F'),
            array('id'=>7, 'parent_id'=>3, 'nickname' => 'G'),
            array('id'=>8, 'parent_id'=>8, 'nickname' => 'H')
        );
        /*
        *2.获取某个会员的无限下级方法
        *$members是所有会员数据表,$mid是用户的id
        */
        $res=$this->GetTeamMember($member ,1);
        var_dump($res);
    }
    function GetTeamMember($members, $mid) {
        $Teams=[];//最终结果
        $mids=array($mid);//第一次执行时候的用户id
        do {
            $othermids=[];
            $state=false;
            foreach ($mids as $valueone) {
                foreach ($members as $key => $valuetwo) {
                    if($valuetwo['parent_id']==$valueone){
                        $Teams[]=$valuetwo['id'];//找到我的下级立即添加到最终结果中
                        $othermids[]=$valuetwo['id'];//将我的下级id保存起来用来下轮循环他的下级
                        array_splice($members,$key,1);//从所有会员中删除他
                        $state=true;
                    }
                }
            }
            $mids=$othermids;//foreach中找到的我的下级集合,用来下次循环
        } while ($state==true);
        return $Teams;
    }
}