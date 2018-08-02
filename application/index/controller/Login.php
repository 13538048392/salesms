<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/19
 * Time: 10:46
 */

namespace app\index\controller;

use app\index\model\User;
use think\Controller;
use think\Cookie;
use think\Db;
use think\Request;
use think\Session;

class Login extends Controller
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
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
            if ($result['active'] == 0) {
                return json(['resp_code' => 3, 'msg' => '账号未激活，请到您的邮箱激活']);
            }
            if ($result == null) {
                return json(['resp_code' => 1, 'msg' => '用户名不存在']);
            }
            if ($result['status'] == 0) {
                $time = 10 * 60 - (time() - strtotime($result['login_time']));
                if ($time > 0) {
                    return json(['resp_code' => '4', 'msg' => '用户已经被锁定请' . $time . '秒后再试']);
                }
                Db::name('user')->where(['id' => $result['id']])->data(['status' => 1, 'error_times' => 0])->update();
            }
            if ($result['error_times'] >= 5) {
                Db::name('user')->where(['id' => $result['id']])->data(['status' => 0])->update();
                return json(['resp_code' => 5, 'msg' => '您的账号已被锁定']);
            }
            if (!password_verify($password, $result['pass'])) {
                Db::name('user')->where(['id' => $result['id']])->data(['error_times' => $result['error_times'] + 1, 'login_time' => time()])->update();
                return json(['resp_code' => 2, 'msg' => '密码不正确，超过五次账号将被锁定十分钟']);
            }
            Db::name('user')->where(['id' => $result['id']])->data(['error_times' => 0])->update();
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
            return json(['msg' => '用户名不能为空', 'status' => 1]);
        }
        if (input('post.email') == '') {
            return json(['msg' => '邮箱不能为空', 'status' => 2]);
        }
        $data['user_name'] = input('post.username');
        $data['email'] = input('post.email');
        $user = new User();
        $res = $user->checkEmail($data);
        if (!$res) {
            return json(['msg' => '用户名和邮箱不匹配', 'status' => 0]);
        }
        $code = input('post.code');
        if ($code != Cookie::get('code')) {
            return json(['msg' => '验证码错误', 'status' => 3]);
        }
        return json(['msg' => '验证成功',
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
            return json(['msg' => '用户名不能为空', 'status' => 1]);
        }
        if (input('post.email') == '') {
            return json(['msg' => '邮箱不能为空', 'status' => 2]);
        }
        if (input('post.password') == '') {
            return json(['msg' => '密码不能为空', 'status' => 3]);
        }
        if (input('post.password') != input('post.password2')) {
            return json(['msg' => '两次输入密码不一致', 'status' => 4]);
        }
        $data['user_name'] = input('post.username');
        $data['email'] = input('post.email');
        $user = new User();
        $res = $user->checkEmail($data);
        if (!$res) {
            return json(['msg' => '用户名和邮箱不匹配', 'status' => 0]);
        }
        $res = $user->resetPass($data, password_hash(input('post.password'), PASSWORD_DEFAULT));

        if ($res) {
            return json(['msg' => '密码重置成功', 'status' => 200]);
        } else {
            return json(['msg' => '密码重置失败', 'status' => 5]);
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
            return json(['msg' => '用户名不能为空', 'status' => 1]);
        }
        if (input('post.email') == '') {
            return json(['msg' => '邮箱不能为空', 'status' => 2]);
        }
        $data['user_name'] = input('post.username');
        $data['email'] = input('post.email');
        $user = new User();
        $res = $user->checkEmail($data);
        if (!$res) {
            return json(['msg' => '用户名和邮箱不匹配', 'status' => 0]);
        }
        $mail = new \Mailer();
        $email = $data['email'];
        $subject = 'Get your password back';
        $code = mt_rand(1000, 9999);
        Cookie::set('code', $code, 3600);
        $body = "您的验证码是{$code}，有效期一个小时，请尽快把验证码输入到网页上完成找回密码操作，如果这条信息不是您本人操作请忽视！";
        $result = $mail->send($email, $subject, $body);
        if ($result) {
            return json(['status' => '1', 'msg' => 'Send the mail successfully, please go to your mailbox to complete the password recovery operation.']); //邮件发送成功
        } else {
            return json(['status' => '0', 'msg' => 'Sending mail failure']);//邮件发送失败
        }
    }

}
