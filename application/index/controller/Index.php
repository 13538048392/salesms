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
    //短信验证码发送
    public function test(){
        ini_set("display_errors", "on"); // 显示错误提示，仅用于测试时排查问题
// error_reporting(E_ALL); // 显示所有错误提示，仅用于测试时排查问题
        set_time_limit(0); // 防止脚本超时，仅用于测试使用，生产环境请按实际情况设置
        header("Content-Type: text/plain; charset=utf-8"); // 输出为utf-8的文本格式，仅用于测试

//      //验证发送短信(SendSms)接口
        $sendSms = new sendSms();
        $code='SB00';
        $phone="13421453520";
        print_r($sendSms->sendSms($code,$phone));

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

    function test233(){
        //
        $short_url = $_SERVER["REQUEST_URI"];
         
        //获取短链
        if ($short_url) {
            $short_url = substr($short_url,1);
            //去除/
            $url = ShortUrlModel::where('short_url',$short_url)->find();
            if ($url) {
                //跳转
                header("location:$url[url]");
            }
            else{
               

                
                header("location:".'http://'.$_SERVER['HTTP_HOST'].'/'.$short_url.'/index');die;
            }
        }
    }

}
