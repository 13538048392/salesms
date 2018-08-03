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
use think\Db;
use think\Loader;
use think\Request;
use think\Session;
use think\Validate;
use app\common\Base;

Loader::import('Mailer', ROOT_PATH . 'application/entend/Mailer.php');

class Register extends Base
{
    public function index(Request $request = null)
    {
        $arr = $request->param();
        if (!isset($arr['id'])&&!isset($arr['role_id'])) {
            //邀请链接不存在
            return $this->error(\think\lang::get('inviting_link_not_exist'));
        }
        $channel = new Channel();
        $result = $channel->channelIsExist($arr['id']);
        if ($result == null) {
            //邀请链接无效
            return $this->error(\think\lang::get('inviting_link_invalid'));
        }
        Session::set('user.parent_id', $result['user_id']);
        Session::set('user.channel_id', $arr['id']);
        Session::set('user_role.role_id',$arr['role_id']);
        return $this->view->fetch('/register');
    }

    public function register()
    {
        if (isset($_POST)) {
            if (!Validate::token('__token__', '', ['__token__' => input('param.__token__')])) {
                //非法请求
                return $this->error(\think\lang::get('unlawful_request'));
            }
            $data = input('post.');
            $validate = new \app\index\validate\User;
            if (!$validate->check($data)) {
                //用户信息错误
                return json(['resp_code' => '1', 'msg' => \think\lang::get('user_error')]); //用户信息填写不完善
            }
            $user = new User();
            $user_id = $user->userRegister($data['username'], password_hash($data['password'], PASSWORD_DEFAULT), $data['email'], Session::get('user.channel_id'),Session::get('user.parent_id'),$data['phone']);
            if (!$user_id) {
                //注册失败，请重新注册
                return json(['resp_code' => '2', 'msg' => \think\lang::get('register_fail')]);
            }
            Db::name('admin_role')->data(['user_id'=>$user_id,'role_id'=>Session::get('role_id')])->insert();
            $password = base64_encode(password_hash($data['password'], PASSWORD_DEFAULT));
            $url = url('index/register/activation', '', '', true);
            $url .= '/username/' . $data['username'] . '/pwd/' . $password;
            $strHtml = '<a href=' . $url . ' target="_blank">' . $url . '</a><br>';
            $subject = \think\lang::get('register_title');
            $body = \think\lang::get('register_email_body') . $strHtml . \think\lang::get('register_email_body2');
            $mail = new \Mailer();
            if ($mail->send($data['email'], $subject, $body)) {
                return json(['resp_code' => '0', 'msg' => \think\lang::get('register_success')]); //邮件发送成功
            } else {
                return json(['resp_code' => '3', 'msg' => \think\lang::get('register_fail')]); //邮件发送失败
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
