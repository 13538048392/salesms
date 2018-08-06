<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/25
 * Time: 19:25
 */

namespace app\index\controller;

use app\admin\model\Role;
use app\admin\model\Url;
use think\Controller;
use think\Db;
use think\Request;
use app\common\Base;

class Channel extends Base
{
    public function index(Request $request)
    {
        $userid = $request->param('userid');
        $channel_id=Db::name('channel')->field('id')->where(['user_id'=>$userid])->find();
        $data= Db::name('channel')
            ->alias('a')
            ->join('url','a.id=b.channel_id')
            ->join('role','b.role_id=c.id')
            ->field('a.channel_name,b.url_code,c.role_name')
            ->where(['a.id'=>$channel_id]);
        return view('/channel', ['data' => $data]);
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
            $arr_role = Db::name('role')->field('id')->where(['type' => 1])->select();
            foreach ($arr_role as $key => $value) {
                if ($value == '3') {
                    $url = "http://" . $_SERVER['SERVER_NAME'] . "/register/index/id/" . $channelId . "/role_id/" . $value;
                } else {
                    $url = "http://47.90.203.241/signup?channelId=" . $channelId . "&referralCode=" . $userId;
                }
                Db::name('url')->data(['channel_id' => $channelId, 'url_code' => $url, 'role_id' => $value])->insert();
                $data = $channel->getChannelById($channelId);
                return json(['resp_code' => 0, 'msg' => $data]);
            }
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
}
