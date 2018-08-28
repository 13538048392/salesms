<?php
namespace app\index\controller;
use duanxin\demo\sendSms;
use think\Controller;
use app\common\Base;
use app\index\model\ShortUrl as ShortUrlModel;

class Index extends Base
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
    public function test(){
        $mailer =new \Mailer();
        $mailer->send('1642504508@qq.com','你好','是的，很好');

    }

    function getShortUrl(){
        //获取短链接
        $api_url = 'http://api.c7.gg/api.php?url=';
        //api接口地址
        $url = str_replace('&', '%26', 'http://47.90.203.241/signup?channelId=88&referralCode=5');
        dump($url);exit;
        $short_url = file_get_contents($url);
        dump($short_url);

    }

    function locationUrl(){
        //
        $short_url = $_SERVER["REQUEST_URI"];
        //获取短链
        if ($short_url) {
            $short_url = substr($short_url,1);
            //去除/
            $url = ShortUrlModel::where('short_url',$short_url)->find();
            if ($url) {
                //跳转
                header("location:$url[url]");die;
            }
            else{
                header("location:".'http://'.$_SERVER['HTTP_HOST'].'/'.$short_url.'/index');die;
            }
        }
    }

}
