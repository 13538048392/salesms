<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/19
 * Time: 10:46
 */

namespace app\index\controller;


use think\Controller;
use think\Request;
use think\Session;

class Login extends Controller
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->redirect('/index/index/register');
    }
    public function index()
    {
        return view('/login');
    }
    public function login()
    {
        return "1111";
    }
}