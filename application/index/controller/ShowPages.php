<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/27
 * Time: 15:21
 */

namespace app\index\controller;


use think\Controller;

class ShowPages extends Controller
{
    /**
     * 注册成功
     */
    public function registerSuccess()
    {
        return view('/register_success');
    }

    /**
     * 会员注册URL失效
     */
    public function cancel()
    {
        return view('/cancel');
    }

    /**
     * 无权限访问
     */
    public function  failAuthorization()
    {
        return view('/failed_authorization');
    }


    /**
     *访问页面不存在
     */
    public function failPage()
    {
        return view('/protected');
    }

}