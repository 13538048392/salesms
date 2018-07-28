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
        if (!isset($arr['channelid'])) {
            return $this->error('404');
        }

        if (!isset($arr['userid']) && !isset($arr['adminid'])) {
            return $this->error('404');
        }

        $_uid = -1;
        if (isset($arr['userid'])) {
            $arr['adminid'] = 0;
            $_uid = $arr['userid'];
        } else {
            $arr['userid'] = 0;
            $_uid = $arr['adminid'];
        }
        $channel = new Channel();
        $result = $channel->channelIsExist($arr['userid'], $arr['adminid'], $arr['channelid']);
        if ($result->isEmpty()) {
            return $this->error('邀请链接无效');
        }
        Session::set('user.userid', $_uid);
        Session::set('user.channelid', $arr['channelid']);
        return $this->view->fetch('/register');
    }

    public function register()
    {
        if (isset($_POST)) {
            if (Validate::token('__token__', '', ['__token__' => input('param.__token__')])) {
                $data = input('post.');
                $validate = new \app\index\validate\User;
                if ($validate->check($data)) {
                    $username = $data['username'];
                    $email = $data['email'];
                    $pwd = $data['password'];
                    $pwd = password_hash($pwd, PASSWORD_DEFAULT);
                    $refererId = Session::get('user.userid');
                    $channelId = Session::get('user.channelid');
                    $user = new User();
                    $result = $user->userRegister($username, $pwd, $email, $refererId, $channelId);
                    if ($result || $result === 0) {
                        $pwd = base64_encode($pwd);
                        $url = url('index/register/activation', '', '', true);
                        $url .= '/username/' . $username . '/pwd/' . $pwd;
                        $strHtml = '<a href=' . $url . ' target="_blank">' . $url . '</a><br>';
                        $subject = '激活码获取';
                        $body = '注册成功，您的激活码是:' . $strHtml . '请点击该地址激活您的用户';
                        $mail = new \Mailer();
                        $result = $mail->send($email, $subject, $body);
                        if ($result) {
                            return json(['resp_code' => '0', 'msg' => 'Sent your message successfully! Please activate your account to your mailbox']); //邮件发送成功
                        } else {
                            return json(['resp_code' => '1', 'msg' => 'Email failed to register again ']); //邮件发送失败
                        }
                    }
                } else {
                    return json(['resp_code' => '2', 'msg' => 'Sorry there was an error sending your form.']); //用户信息填写不完善
                }
            } else {
                return $this->error('非法请求'); //非法提交表达
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
