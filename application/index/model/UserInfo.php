<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/23
 * Time: 12:53
 */

namespace app\index\model;


use think\Model;

class UserInfo extends Model
{
    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    public function updateUserInfo($userid, $firstname, $lastname, $phone, $address, $wechat, $gender)
    {
        $data = [
            'user_id' => $userid,
            'first_name' => $firstname,
            '$last_name' => $lastname,
            'phone' => $phone,
            'address' => $address,
            'wechat' => $wechat,
            'gender' => $gender
        ];
        $this->data($data)->save();
    }

}