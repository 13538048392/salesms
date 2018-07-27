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
        $data = $channel->getChannelById($userid);
//        print_r($data);
//        exit;
        return view('/channel', ['data' => $data]);
    }

    public function addChannel(Request $request)
    {
        if (isset($_POST)) {
            $userId = $request->param('userid');
            $channelName = input('channel_name');
            $channel = new \app\index\model\Channel();
            $num = $channel->getChannelNumById($userId);
            if ($num < 10) {
                $result = $channel->addChannel($userId, 0, $channelName);
                if($result){
                    return "<script>location.href='".$_SERVER["HTTP_REFERER"]."';</script>";
                }
            }else{
                return "<script>alert('最多添加十个');location.href='".$_SERVER["HTTP_REFERER"]."';</script>";
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
                return "<script>location.href='".$_SERVER["HTTP_REFERER"]."';</script>";
            }
        }
    }
}