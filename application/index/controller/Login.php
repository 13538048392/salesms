<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/19
 * Time: 10:46
 */

namespace app\index\controller;

use app\index\model\User;
use think\Config;
use think\Controller;
use think\Cookie;
use think\Db;
use think\Request;
use think\Session;
use app\common\Base;

class Login extends Base
{
    private $redis;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->redis=new \Redis();
        $this->redis->connect(Config::get('redis.host'),Config::get('redis.port'));
        $this->redis->select(Config::get('redis.db_index'));
    }

    public function index()
    {
        if (!Session::has('user.username')) {
            if(!Cookie::has('username')){
               // return '111';
                return view('/login');
            }else{
               // return '1111';
                Session::set('userid',Cookie::get('userid'));
                $this->redirect('channel/index', ['userid' => session('userid')]);
            }
        }else{
            $this->redirect('channel/index', ['userid' => session('userid')]);
        }

    }

    public function login()
    {
        if (isset($_POST)) {
            $username = input('username');
            $password = input('password');

            $checkPhone = "/^((13[0-9])|(14[5|7])|(15([0-3]|[5-9]))|(18[0-9])|(17[0-9])|(19[0-9])|16[6])\d{8}$/";
            $user = new User();

            if (preg_match($checkPhone, $username)) {
                $result = $user->userPhoneLogin($username);
            } else {
                $result = $user->userNameLogin($username);
            }
            
            if ($result == null) {
                //用户名不存在
                return json(['resp_code' => 1, 'msg' => \think\lang::get('user_not_exist')]);
            }
            if ($result['active'] == 0) {
                //账号未激活
                return json(['resp_code' => 3, 'msg' => \think\lang::get('user_not_activate').\think\lang::get('no_active')." <a href='#' onclick='send_email()'>".\think\lang::get('send_email_again').'</a>']);
            }

            if($result['status'] == 0){
                return json(['resp_code' => '4', 'msg' => \think\lang::get('user_frozen')]);
            }

            if($this->redis->get('user:'.$username)>=5){
                $this->redis->expire('user:'.$username,'600');
                return json(['resp_code' => 5, 'msg' => \think\lang::get('user_frozen_seconds')]);
            }

            if (!password_verify($password, $result['pass'])) {

                if($this->redis->exists('user:'.$username)){
                    $this->redis->incr('user:'.$username);
                }else{
                    $this->redis->set('user:'.$username,1);
                }
                return json(['resp_code' => 2, 'msg' => \think\lang::get('password_error')]);
            }
            if(input('remember')=='on'){
               Cookie::set('username',$username,30*24*60*60);
               Cookie::set('userid',$result['id'],30*24*60*60);
            }

            Session::set('user.username', $username);
            Session::set('userid', $result['id']);
            return json(['resp_code' => 0, 'user_id' => $result['id']]);
        }
    }

    public function loginOut()
    {
        Session::set('user.username', null);
        Session::set('userid', null);
        Cookie::delete('username');
        Cookie::delete('userid');
        $this->redirect('ShowPages/loginOut');
    }


    public function searchPass()
    {
        //找回密码界面
        return view('/search_pass');
    }

    public function doSearchPass()
    {
        //找回密码执行
        // if (input('post.username') == '') {
        //     //用户名不能为空
        //     return json(['msg' => \think\lang::get('user_not_null'), 'status' => 1]);
        // }
        // if (input('post.email') == '') {
        //     //email不能为空
        //     return json(['msg' => \think\lang::get('email_not_null'), 'status' => 2]);
        // }

        if (input('post.phone') == '') {
            //email不能为空
            return json(['msg' => \think\lang::get('phone_not_null'), 'status' => 2]);
        }
        // $data['user_name'] = input('post.username');
        // $data['email'] = input('post.email');
        // $user = new User();
        // $res = $user->checkEmail($data);
        $res = User::where('phone',input('post.phone'))->find();
        // if (!$res) {
        //     //用户名和邮箱不匹配
        //     return json(['msg' => \think\lang::get('check_email_user'), 'status' => 0]);
        // }
        if (!$res) {
            return json(['msg' => '电话号码不存在', 'status' => 0]);
        }
        $code = input('post.code');
        // if ($code != Cookie::get('code')) {
        //     //验证码错误
        //     return json(['msg' => \think\lang::get('verify_code_error'), 'status' => 3]);
        // }
        if ($this->redis->get('user:' . input('post.phone')) === input('post.code')) {
            //验证成功
            return json(['msg' => \think\lang::get('verify_success'),
                         'status' => 200,
                         'phone' => input('post.phone')]);
        }
        else{
            return json(['msg' => \think\lang::get('verify_code_error'), 'status' => 3]);
        }
        
    }

    public function resetPass()
    {
        //密码重置
        return view('/reset_pass');
    }

    public function doResetPass()
    {
        //密码重置执行
        // if (input('post.username') == '') {
        //     return json(['msg' => \think\lang::get('user_not_null'), 'status' => 1]);
        // }
        // if (input('post.email') == '') {
        //     return json(['msg' => \think\lang::get('email_not_null'), 'status' => 2]);
        // }
        if (input('post.phone') == '') {
            return json(['msg' => \think\lang::get('phone_not_null'), 'status' => 2]);
        }

        if (input('post.password') == '') {
            return json(['msg' => \think\lang::get('pass_not_null'), 'status' => 3]);
        }
        if (input('post.password') != input('post.password2')) {
            return json(['msg' => \think\lang::get('two_pass_differ'), 'status' => 4]);
        }
        // $data['user_name'] = input('post.username');
        // $data['email'] = input('post.email');
        $user = new User();
        // $res = $user->checkEmail($data);
        $res = User::where('phone',input('post.phone'))->find();
        if (!$res) {
            return json(['msg' => '手机号码不存在', 'status' => 0]);
        }

        $res = $user->resetPass($res['phone'], password_hash(input('post.password'), PASSWORD_DEFAULT));

        if ($res) {
            return json(['msg' => \think\lang::get('pass_reset_success'), 'status' => 200]);
        } else {
            return json(['msg' => \think\lang::get('pass_reset_fail'), 'status' => 5]);
        }

    }

    public function checkResetCode()
    {
        $code = input('verify');
        if ($code == Cookie::get('code')) {
            echo json_encode(['valid' => true]);
        } else {
            echo json_encode(['valid' => false]);
        }
    }

    public function sendEmail()
    {
        //发送找回密码邮件
        if (input('post.username') == '') {
            return json(['msg' => \think\lang::get('user_not_null'), 'status' => 1]);
        }
        if (input('post.email') == '') {
            return json(['msg' => \think\lang::get('email_not_null'), 'status' => 2]);
        }
        $data['user_name'] = input('post.username');
        $data['email'] = input('post.email');
        $user = new User();
        $res = $user->checkEmail($data);
        if (!$res) {
            return json(['msg' => \think\lang::get('check_email_user'), 'status' => 0]);
        }
        $mail = new \Mailer();
        $email = $data['email'];
        $subject = \think\lang::get('email_title');
        $code = mt_rand(1000, 9999);
        Cookie::set('code', $code, 3600);
        $body = \think\lang::get('email_body_one')." ({$code}),".\think\lang::get('email_body_two');
        $result = $mail->send($email, $subject, $body);
        if ($result) {
            return json(['status' => '1', 'msg' => \think\lang::get('send_email_success')]); //邮件发送成功
        } else {
            return json(['status' => '0', 'msg' => \think\lang::get('send_email_fail')]);//邮件发送失败
        }
    }

    public function sendEmailAgain(){
        //重新发送邮件
        $username = input('post.username');
        $password = input('post.password');

        $checkEmail = "/\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/";
            $checkPhone = "/^((13[0-9])|(14[5|7])|(15([0-3]|[5-9]))|(18[0,5-9]))\d{8}$/";
            $user = new User();
            if (preg_match($checkEmail, $username)) {
                $result = $user->userEmailLogin($username);
            } elseif (preg_match($checkPhone, $username)) {
                $result = $user->userPhoneLogin($username);
            } else {
                $result = $user->userNameLogin($username);
            }
            
            if ($result == null) {
                //用户名不存在
                return json(['resp_code' => 1, 'msg' => \think\lang::get('user_not_exist')]);
            }
           
            if (!password_verify($password, $result['pass'])) {

                return json(['resp_code' => 2, 'msg' => \think\lang::get('password_error')]);
            }

            return sendEmail($password,$result['user_name'],$result['email']);
    }



}
