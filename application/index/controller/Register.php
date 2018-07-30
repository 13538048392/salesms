<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/19
 * Time: 10:47
 */

namespace app\index\controller;

use app\index\model\Channel;
use app\index\model\User;
use think\Controller;
use think\Loader;
use think\Request;
use think\Session;
use think\Validate;

Loader::import('Mailer', ROOT_PATH . 'application/entend/Mailer.php');

class Register extends Controller
{
    public function index(Request $request = null)
    {
        $arr = $request->param();
        if (!isset($arr['id']) && !isset($arr['type'])) {
            return $this->error('邀请链接不存在');
        }
        $channel = new Channel();
        $result = $channel->channelIsExist($arr['id']);
        if ($result == null) {
            return $this->error('邀请链接无效');
        }
        Session::set('user.id', $result['user_id']);
        Session::set('channel.id', $arr['id']);
        Session::set('user.type', $arr['type']);
        return $this->view->fetch('/register');
    }

    public function register()
    {
        if (isset($_POST)) {
            if (!Validate::token('__token__', '', ['__token__' => input('param.__token__')])) {
                return $this->error('非法请求');
            }
            $data = input('post.');
            $validate = new \app\index\validate\User;
            if (!$validate->check($data)) {
                return json(['resp_code' => '1', 'msg' => '用户信息不正确']); //用户信息填写不完善
            }
            $user = new User();
            $result = $user->userRegister($data['username'], password_hash($data['password'], PASSWORD_DEFAULT), $data['email'], $data['phone'], Session::get('channel.id'), Session::get('user.type'));
            if (!$result) {
                return json(['resp_code' => '2', 'msg' => '注册失败请重新注册 ']);
            }
            $password = base64_encode(password_hash($data['password'], PASSWORD_DEFAULT));
            $url = url('index/register/activation', '', '', true);
            $url .= '/username/' . $data['username'] . '/pwd/' . $password;
            $strHtml = '<a href=' . $url . ' target="_blank">' . $url . '</a><br>';
            $subject = '激活码获取';
            $body = '注册成功，您的激活码是:' . $strHtml . '请点击该地址激活您的用户';
            $mail = new \Mailer();
            if ($mail->send($data['email'], $subject, $body)) {
                return json(['resp_code' => '0', 'msg' => '注册成功，请到邮箱激活您的账号']); //邮件发送成功
            } else {
                return json(['resp_code' => '3', 'msg' => '邮件发送失败，请重新注册 ']); //邮件发送失败
            }

        }
    }

    public function checkPhone()
    {
        if (isset($_POST)) {
            $phone = input('phone');
            $user = new User();
            $result = $user->phoneIsExist($phone);
            if ($result) {
                echo json_encode(['valid' => false]);
            } else {
                echo json_encode(['valid' => true]);
            }
        }
    }

    public function checkUser()
    {
        if (isset($_POST)) {
            $userName = input('username');
            $user = new User();
            $result = $user->userNameIsExist($userName);
            if ($result) {
                echo json_encode(['valid' => false]);
            } else {
                echo json_encode(['valid' => true]);
            }
        }
    }

    public function checkEmail()
    {
        if (isset($_POST)) {
            $email = input('email');
            $user = new User();
            $result = $user->userEmailIsExist($email);
            if ($result) {
                echo json_encode(['valid' => false]);
            } else {
                echo json_encode(['valid' => true]);
            }
        }
    }

    public function activation(Request $request)
    {
        $arr = $request->param();
        $username = $request->param('username');
        $pwd = base64_decode($request->param('pwd'));
        if (!empty($username) && !empty($pwd)) {
            $user = new User();
            $result = $user->userActivation($username, $pwd);
            if ($result !== false) {
                $this->redirect('/login/index');
            }
        }
    }

}
