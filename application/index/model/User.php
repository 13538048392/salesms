<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/19
 * Time: 17:48
 */

namespace app\index\model;


use think\Model;
use think\Db;

class User extends Model
{
    public function userRegister($username, $password, $channel_id, $parent_id, $phone)
    {
        $data = [
            'user_name' => $username,
            'pass' => $password,
            'channel_id' => $channel_id,
            'parent_id' => $parent_id,
            'phone' => $phone
        ];
        return $this->insertGetId($data);
    }

    public function userActivation($username, $password)
    {
        if (isset($username, $password) && !empty($username) && !empty($password)) {
            $result=$this->where(['user_name'=>$username])->field('pass')->find();
            if (password_verify($password, $result['pass'])){
                return $this->where(['user_name' => $username])->update(['active' => 1]);
            }
        }
    }

    public function userEmailIsExist($email)
    {
        if (isset($email) && !empty($email)) {
            return $this->where(['email' => $email])->find();

        }
    }

    public function userNameIsExist($username)
    {
        if (isset($username) && !empty($username)) {
            return $this->where(['user_name' => $username])->find();
        }
    }

    public function phoneIsExist($phone)
    {
        if (isset($phone) && !empty($phone)) {
            return $this->where(['phone' => $phone])->find();
        }
    }

    public function vertifyCookie($username, $password)
    {
        if (isset($username, $password) && !empty($username) && !empty($password)) {
            return $this->where(['user_name' => $username, 'pass' => $password])->find();
        }
    }

    public function userNameLogin($username)
    {
        if (isset($username) && !empty($username)) {
            return $this->where(['user_name' => $username])->find();
        }
    }

    public function userEmailLogin($email)
    {
        if (isset($email) && !empty($email)) {
            return $this->where(['email' => $email])->find();
        }
    }

    public function userPhoneLogin($phone)
    {
        if (isset($phone) && !empty($phone)) {
            return $this->where(['phone' => $phone])->find();
        }
    }

    public function checkEmail($data)
    {
        //检测邮箱和用户
        $res = User::where($data)->find();
        return $res;
    }

    public function resetPass($phone, $password)
    {
        //修改密码

        $res = User::where('phone',$phone)->update(['pass' => $password]);

        return $res;
    }
}