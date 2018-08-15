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
            return view('/login');
        }
        $this->redirect('home/index', ['userid' => session('userid')]);
    }

    public function login()
    {
        if (isset($_POST)) {
            $username = input('username');
            $password = input('password');
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
            if ($result['active'] == 0) {
                //账号未激活
                return json(['resp_code' => 3, 'msg' => \think\lang::get('user_not_activate')]);
            }

            if($result['status'] == 0){
                return json(['resp_code' => '4', 'msg' => \think\lang::get('user_frozen')]);
            }

            if($this->redis->get('user:'.$username)>=5){
                $this->redis->expire('user:'.$username,'600');
                return json(['resp_code' => 5, 'msg' => \think\lang::get('user_frozen_seconds')]);
            }
//            if ($result['status'] == 0) {
//                $time = 10 * 60 - (time() - strtotime($result['login_time']));
//                if ($time > 0) {
//                    //用户被锁定
//                    return json(['resp_code' => '4', 'msg' => \think\lang::get('user_frozen') . $time . \think\lang::get('user_frozen_seconds')]);
//                }
//                Db::name('user')->where(['id' => $result['id']])->data(['status' => 1, 'error_times' => 0])->update();
//            }
//            if ($result['error_times'] >= 5) {
//                Db::name('user')->where(['id' => $result['id']])->data(['status' => 0])->update();
//                //账号被锁定
//                return json(['resp_code' => 5, 'msg' => \think\lang::get('user_frozen')]);
//            }
            if (!password_verify($password, $result['pass'])) {
               // Db::name('user')->where(['id' => $result['id']])->data(['error_times' => $result['error_times'] + 1, 'login_time' => time()])->update();
                if($this->redis->exists('user:'.$username)){
                    $this->redis->incr('user:'.$username);
                }else{
                    $this->redis->set('user:'.$username,1);
                }
                return json(['resp_code' => 2, 'msg' => \think\lang::get('password_error')]);
            }
           // Db::name('user')->where(['id' => $result['id']])->data(['error_times' => 0])->update();
            Session::set('user.username', $username);
            Session::set('userid', $result['id']);
            return json(['resp_code' => 0, 'user_id' => $result['id']]);
        }
    }

    public function loginOut()
    {
        Session::set('user.username', null);
        Session::set('userid', null);
        $this->redirect('/index');
    }


    public function searchPass()
    {
        //找回密码界面
        return view('/search_pass');
    }

    public function doSearchPass()
    {
        //找回密码执行
        if (input('post.username') == '') {
            //用户名不能为空
            return json(['msg' => \think\lang::get('user_not_null'), 'status' => 1]);
        }
        if (input('post.email') == '') {
            //email不能为空
            return json(['msg' => \think\lang::get('email_not_null'), 'status' => 2]);
        }
        $data['user_name'] = input('post.username');
        $data['email'] = input('post.email');
        $user = new User();
        $res = $user->checkEmail($data);
        if (!$res) {
            //用户名和邮箱不匹配
            return json(['msg' => \think\lang::get('check_email_user'), 'status' => 0]);
        }
        $code = input('post.code');
        if ($code != Cookie::get('code')) {
            //验证码错误
            return json(['msg' => \think\lang::get('verify_code_error'), 'status' => 3]);
        }
        //验证成功
        return json(['msg' => \think\lang::get('verify_success'),
            'status' => 200,
            'user_name' => $data['user_name'],
            'email' => $data['email']]);
    }

    public function resetPass()
    {
        //密码重置
        return view('/reset_pass');
    }

    public function doResetPass()
    {
        //密码重置执行
        if (input('post.username') == '') {
            return json(['msg' => \think\lang::get('user_not_null'), 'status' => 1]);
        }
        if (input('post.email') == '') {
            return json(['msg' => \think\lang::get('email_not_null'), 'status' => 2]);
        }
        if (input('post.password') == '') {
            return json(['msg' => \think\lang::get('pass_not_null'), 'status' => 3]);
        }
        if (input('post.password') != input('post.password2')) {
            return json(['msg' => \think\lang::get('two_pass_differ'), 'status' => 4]);
        }
        $data['user_name'] = input('post.username');
        $data['email'] = input('post.email');
        $user = new User();
        $res = $user->checkEmail($data);
        if (!$res) {
            return json(['msg' => \think\lang::get('check_email_user'), 'status' => 0]);
        }
        $res = $user->resetPass($data, password_hash(input('post.password'), PASSWORD_DEFAULT));

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

}
