<?php
namespace app\index\controller;

use think\Controller;

class Index extends Controller
{
    public function index()
    {
        return view('/index');
    }
    public function register()
    {
        return "this is register method";
    }
    public function login()
    {
        return "this is login method";
    }
    //二维码生成示范
    public function  showQR(){
        $content ="https://www.baidu.com/";
        $file='qrcode'.time().'.png';
        \Qrcode\MyQrcode::showQrcode($content,$file);
    }
}
