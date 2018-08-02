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

    public function addUserInfo($userid, $firstname, $lastname, $address, $wechat, $gender)
    {
        $data = [
            'user_id' => $userid,
            'first_name' => $firstname,
            'last_name' => $lastname,
            'address' => $address,
            'wechat' => $wechat,
            'gender' => $gender
        ];
        return $this->data($data)->save();
    }

    public function updateUserInfo($userid, $firstname, $lastname, $address, $wechat, $gender)
    {
        $data = [
            'first_name' => $firstname,
            'last_name' => $lastname,
            'phone' => $phone,
            'address' => $address,
            'wechat' => $wechat,
            'gender' => $gender
        ];
        return $this->where(['user_id' => $userid])->data($data)->update();
    }

    public function getUserInfoById($usreid)
    {
        return $this->limit(1)->where(['user_id' => $usreid])->find();
    }

    public function idIsExist($userid)
    {
        return $this->field("user_id")->where(['user_id'=>$userid])->find();
    }

}