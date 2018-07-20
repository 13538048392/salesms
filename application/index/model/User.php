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
    public function __construct($data = [])
    {
        parent::__construct($data);
        $user=Loader::model('User');
    }
    public function userPhoneIsExist($where)
    {
        if (isset($where) && !empty($where)) {
            $result = Db::table('user')->field('email,active,status')->where($where)->limit(1)->select();
            $array = $this->table('user')->field('a.email,a.active,a.status,b.phone')->alias(['user' => 'a', 'userinfo' => 'b'])->join('userinfo', 'a.user_id=b.user_id')->limit(1)->select();
        }
    }
    public function  userEmailIsExist($where)
    {
        if(isset($where)&& !empty($where)){
            $result = Db::table('user')->field('email,active,status')->where($where)->limit(1)->select();
        }
    }
}