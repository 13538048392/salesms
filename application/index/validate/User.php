<?php
/**
 * Created by PhpStorm.
 * User: MAIBENBEN
 * Date: 2018/7/22
 * Time: 12:18
 */

namespace app\index\validate;


use think\Validate;

class User extends Validate
{
    protected $rules=[
        'username'=>['require','min'=>6,'max'=>18,'regex'=>'/^[a-zA-Z0-9_\-]+$/'],
        'email'=>'email',
        'phone'=>'mobile',
        'password'=>['require','confirm'],
        'confirm_password'=>['require','confirm'=>'password'],
    ];
    protected $msg = [
        'username.require' => '用户名不能为空',
        'username.min'=>'用户名最少为6个字符',
        'username.max' => '用户名最多为18个字符',
        'username.regex'=>'用户名只能包含大写、小写、数字、下划线和-',
        'email' => '邮箱格式错误',
        'phone'=>'手机号码不正确',
        'password.require'=>'密码不能为空',
        'confirm_password.require'=>'密码不能为空',
        'confirm_password.confirm'=>'两次密码不一致',
    ];
}





