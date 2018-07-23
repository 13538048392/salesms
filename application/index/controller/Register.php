<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/19
 * Time: 10:47
 */

namespace app\index\controller;
use think\Controller;
use think\Loader;
use think\Request;
use think\Validate;
use app\index\model\user;
Loader::import('Mailer',ROOT_PATH  . 'application/entend/Mailer.php');

class Register extends Controller
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
//        $arr=$request->param();
//        if(!empty($arr)&&isset($arr['userid'],$arr['channelid']))
//        {
//            $userid=$arr['usreid'];
//            $channelid=$arr['channelid'];
//            echo $userid,$channelid;
//        }
    }

    public function index()
    {
        return $this->view->fetch('/register');
    }

    public function register()
    {
        if (isset($_POST)) {
            if (Validate::token('__token__','',['__token__' => input('param.__token__')])) {
                $data = input('post.');
                $validate = new \app\index\validate\User;
                if ($validate->check($data)) {
                    $username=$data['username'];
                    $email=$data['email'];
                    $pwd=$data['password'];
                    $pwd=password_hash($pwd,PASSWORD_DEFAULT);
                    $pwd = base64_encode($pwd);
                    $url = url('index/register/activation','','',true);
                    $url .= '/' . $username . '/' . $pwd;
                    $strHtml='<a href=' . $url . ' target="_blank">' . $url . '</a><br>';
                    $subject='激活码获取';
                    $body='注册成功，您的激活码是:' . $strHtml . '请点击该地址激活您的用户';
                    $mail=new \Mailer();
                    $mail->send($email,$subject,$body);
                    return 'ok';
                }
            } else {
                return $this->error('404');
            }
        }
    }
    public function checkUser()
    {

    }
    public function checkCode()
    {

    }
    public function activation()
    {
        return '1111';
    }
}