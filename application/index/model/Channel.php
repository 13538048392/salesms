<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/23
 * Time: 12:54
 */

namespace app\index\model;

use think\Model;

class Channel extends Model
{
    public function channelIsExist($id)
    {
        $where = ['id' => $id];
        return $this->where($where)->find();
    }

    public function getChannelNumById($userId)
    {
        return $this->where(['user_id' => $userId])->count();
    }

    public function getChannelById($channelId)
    {
        return $this->where(['channel_id' => $channelId])->find();
    }

    public function getChannelByUserId($userid)
    {
        return $this->where(['user_id' => $userid])->select();
    }

    public function addChannel($userID, $channelName)
    {
        $data = ['user_id' => $userID,'channel_name' => $channelName];
        return $channelId = Channel::insertGetId($data);
    }

    public function deleteChannel($channeId)
    {
        return $this->where(['channel_id' => $channeId])->delete();
    }
}
