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
    public function channelIsExist($userId, $adminId, $channelId)
    {
        $where = ['channel_id' => $channelId];
        if ($userId !== 0) {
            $where['user_id'] = $userId;
            return $this->where($where)->select();
        }
        $where['admin_id'] = $adminId;
        return $this->where($where)->select();
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
    public function UpdateByChannelId($channelId, $urlCode)
    {
        return $this->where(['channel_id' => $channelId])->data(['url_code' => $urlCode])->update();
    }

    public function addChannel($userID, $adminId, $channelName)
    {
        $data = ['user_id' => $userID, 'admin_id' => $adminId, 'channel_name' => $channelName];
        return $channelId = Channel::insertGetId($data);
    }

    public function deleteChannel($channeId)
    {
        return $this->where(['channel_id' => $channeId])->delete();
    }
}
