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
        if ($userId !== 0) {
            return $this->limit(1)->where(['channel_id' => $channelId, 'user_id' => $userId])->select();
        } else {
            return $this->limit(1)->where(['channel_id' => $channelId, 'admin_id' => $adminId])->select();
        }
    }

    public function getChannelNumById($userId)
    {
        return $this->where(['user_id' => $userId])->count();
    }

    public function getChannelById($userId)
    {
        return $this->where(['user_id'=>$userId])->select();
    }

    public function addChannel($userID, $adminId, $channelName)
    {
        $data =['user_id'=>$userID,'admin_id'=>$adminId,'channel_name'=>$channelName];
        return $this->save($data);
    }

    public function deleteChannel($channeId)
    {
        return $this->where(['channel_id'=>$channeId])->delete();
    }
}