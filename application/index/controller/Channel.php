<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/25
 * Time: 19:25
 */

namespace app\index\controller;

use think\Controller;
use think\Request;

class Channel extends Controller
{
    public function index(Request $request)
    {
        $userid = $request->param('userid');
        $channel = new \app\index\model\Channel();
        $data = $channel->getChannelByUserId($userid);
        return view('/channel', ['data' => $data->toArray()]);
    }

    public function addChannel(Request $request)
    {
        if (isset($_POST)) {
            $userId = $request->param('userid');
            $channelName = input('channel_name');
            $channel = new \app\index\model\Channel();
            $num = $channel->getChannelNumById($userId);
            if ($num < 10) {
                $channelId = $channel->addChannel($userId, 0, $channelName);
                if ($channelId) {
                    $url = "http://" . $_SERVER['SERVER_NAME'] . "/register/index/userid/$userId/channelid/$channelId";
                    $result = $channel->UpdateByChannelId($channelId, $url);
                    if ($result) {
                        $data = $channel->getChannelById($channelId);
                        return json(['resp_code' => 0, 'msg' => $data]);
                    }
                }
            } else {
                return json(['resp_code' => 1, 'msg' => '最多增加10个渠道']);
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
