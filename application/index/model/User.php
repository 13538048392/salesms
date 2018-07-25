<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/19
 * Time: 17:48
 */

namespace app\index\model;


use think\Loader;
use think\Model;

class User extends Model
{
    public function userRegister($username, $password, $email, $referee_id, $channel_id)
    {
        $this->data = [
            'user_name' => $username,
            'pass' => $password,
            'email' => $email,
            'referee_id'=>$referee_id,
            'channel_id'=>$channel_id
        ];
        return $this->save();
    }

    public function userActivation($username, $password)
    {
        if (isset($username, $password) && !empty($username) && !empty($password)) {
            return $this->where(['user_name' => $username, 'pass' => $password])->update(['active' => 1]);
        }
    }

    public function userEmailIsExist($email)
    {
        if (isset($email) && !empty($email)) {
            return $this->limit(1)->where(['email' => $email])->find();

        }
    }

    public function userNameIsExist($username)
    {
        if (isset($username) && !empty($username)) {
            return $this->limit(1)->where(['user_name' => $username])->find();
        }
    }
}