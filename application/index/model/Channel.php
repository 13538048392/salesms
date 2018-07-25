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
    public function channelIsExist($userId, $channelId)
    {
        return $this->limit(1)->where(['channel_id' => $channelId, 'user_id' => $userId])->select();
    }
}