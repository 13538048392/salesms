<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/25
 * Time: 19:25
 */

namespace app\index\controller;
use think\Loader;
use app\admin\model\Role;
use app\admin\model\Url;
use think\Controller;
use think\Db;
use think\Request;
Loader::import('QueryingCode', ROOT_PATH . 'application/entend/QueryingCode.php');

class Channel extends Controller
{
    public function index(Request $request)
    {
        $userid = $request->param('userid');
        $channel = Db::name('channel')->where(['user_id' => $userid])->select();
        foreach ($channel as $key=>$value)
        {
            $data = Db::name('url')
                ->alias('b')
                ->join('channel a', 'a.id=b.channel_id')
                ->join('role c', 'b.role_id=c.id')
                ->field('b.url_code,c.role_name')
                ->where('b.channel_id',$value['id'])
                ->select();
            $channel[$key]['url_code']=$data;
        }
        return view('/channel', ['data' => $channel]);
    }

    public function addChannel(Request $request)
    {
        if (isset($_POST)) {
            $userId = $request->param('userid');
            $channelName = input('channel_name');
            $channel = new \app\index\model\Channel();
            $num = $channel->getChannelNumById($userId);
            if ($num > 10) {
                return json(['resp_code' => 1, 'msg' => '最多增加10个渠道']);
            }
            $channelId = $channel->addChannel($userId, $channelName);
            $arr_role = Db::name('admin_role')->field('role_id')->where(['user_id' => $userId])->select();
            foreach ($arr_role as $key => $value) {
                    switch ($value['role_id']) {
                        //如果是角色是医生
                        case 2:
                            $url_doctor = "http://47.90.203.241/signup?channelId=" . $channelId . "&referralCode=" . $userId;
                            $data = [
                                'channel_id' => $channelId,
                                'url_code' => $url_doctor,
                                'role_id' => $value['role_id']
                            ];
                            Db::name('url')->insert($data);
                            break;
                        //如果角色是销售员
                        case 3:
                            $url_sale = "http://" . $_SERVER['SERVER_NAME'] . "/register/index/id/" . $channelId . "/role_id/" . $value['role_id'];
                            $url_doctor = "http://47.90.203.241/signup?channelId=" . $channelId . "&referralCode=" . $userId;
                            $data = [
                                [
                                    'channel_id' => $channelId,
                                    'url_code' => $url_sale,
                                    'role_id' => $value['role_id']
                                ],
                                [
                                    'channel_id' => $channelId,
                                    'url_code' => $url_doctor,
                                    'role_id' => 2
                                ]
                            ];
                            Db::name('url')->insertAll($data);
                            break;
                }
            }
            return json(['resp_code' => 0, 'msg' => '添加成功']);
        }
    }

    public function deleteChannel(Request $request)
    {
        if ($request) {
            $channelId = $request->param('channel_id');
            $channel = new \app\index\model\Channel();
            $result = $channel->deleteChannel($channelId);
            if ($result && $result !== false) {
                return json(['resp_code' => 0, 'msg' => 'true']);
            }
        }
    }

    public function queryingCode()
    {
        $code=new \QueryingCode();
        $code->makeQueryingCode('https://www.baidu.com');
    }
}
