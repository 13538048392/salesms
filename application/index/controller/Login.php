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
        if(isset($_SESSION['username'])&& !empty($_SESSION['username'])){
            $this->redirect('/index.php/index/index');
        }elseif(isset($_COOKIE['username'])&&!empty($_COOKIE['username'])){
            $username=$_COOKIE['username'];
            $password=$_COOKIE['password'];
            
            $this->redirect('/index.php/index/index');
        }

    }
    public function index()
    {
        return view('/login');
    }
    public function login()
    {

    }
}