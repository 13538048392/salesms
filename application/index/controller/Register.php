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
use think\Config;
use think\Controller;
use think\Db;
use think\Loader;
use think\Request;
use think\Session;
use think\Validate;
use app\common\Base;

Loader::import('ShortMessage', ROOT_PATH . 'application/entend/ShortMessage.php');
Loader::import('Mailer', ROOT_PATH . 'application/entend/Mailer.php');

class Register extends Base
{
    private $redis;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->redis = new \Redis();
        $this->redis->connect(Config::get('redis.host'), Config::get('redis.port'));
        $this->redis->select(Config::get('redis.db_index'));
    }


    public function index(Request $request = null)
    {
        $arr = $request->param();
        if (!isset($arr['id']) && !isset($arr['role_id'])) {
            //邀请链接不存在
            return redirect('ShowPages/failPage');
        }
        $channel = new Channel();
        $result = $channel->channelIsExist($arr['id']);
        if ($result == null) {
            //邀请链接无效
            return redirect('ShowPages/cancel');
        }
        Session::set('user.parent_id', $result['user_id']);
        Session::set('user.channel_id', $arr['id']);
        Session::set('user_role.role_id', $arr['role_id']);
        return $this->view->fetch('/register');
    }


    public function register()
    {
        if (isset($_POST)) {
            if (!Validate::token('__token__', '', ['__token__' => input('param.__token__')])) {
                //非法请求
                return redirect('ShowPages/failAuthorization');
            }
            $data = input('post.');
            $validate = new \app\index\validate\User;
            if (!$validate->check($data)) {
                //用户信息错误
                return json(['resp_code' => '1', 'msg' => \think\lang::get('user_error')]); //用户信息填写不完善
            }
//            $password = urlsafe_b64encode($data['password']);
//            $url = url('index/register/activation', '', '', true);
//            $url .= '/username/' . $data['username'] . '/pwd/' . $password;
//            $strHtml = '<a href=' . $url . ' target="_blank">' . $url . '</a><br>';
//            $subject = \think\lang::get('register_title');
//            $body = \think\lang::get('register_email_body') . $strHtml . \think\lang::get('register_email_body2');
//            $mail = new \Mailer();
//            if (!$mail->send($data['email'], $subject, $body)) {
//                return json(['resp_code' => '3', 'msg' => \think\lang::get('register_fail')]); //邮件发送失败
//            }
            $user = new User();
            $user_id = $user->userRegister($data['username'], password_hash($data['password'], PASSWORD_DEFAULT), Session::get('user.channel_id'), Session::get('user.parent_id'), $data['phone']);
            if (!$user_id) {
                //注册失败，请重新注册
                return json(['resp_code' => '2', 'msg' => \think\lang::get('register_fail')]);
            }
            Db::name('admin_role')->data(['user_id' => $user_id, 'role_id' => Session::get('user_role.role_id')])->insert();
            $this->redis->del('user:' . input('phone'));
            return json(['resp_code' => '0', 'msg' => \think\lang::get('register_success')]);

        }
    }


    public  function testBoot()
    {
        return view('/test');
    }


    public function testEmail()
    {
        $mail=new \Mailer();
        $mail->send('804310470@qq.com', '1111111111', '804310470@qq.com');

    }

    public function  testPhp()
    {
        echo phpinfo();
    }

    /**
     * 检查用户名是否存在
     */
    public function checkUser()
    {
        if (isset($_POST)) {
            $userName = input('username');
            $user = new User();
            $result = $user->userNameIsExist($userName);
            if ($result) {
                return json(['valid' => false]);
            }
            return json(['valid' => true]);
        }
    }

    /**
     * 检查电话号码是否存在
     */
    public function checkPhone()
    {
        if (isset($_POST)) {
            $email = input('phone');
            $user = new User();
            $result = $user->phoneIsExist($email);
            if ($result) {
                return json(['valid' => false]);
            }
            return json(['valid' => true]);

        }
    }

    /**
     * 检查邮箱是否存在
     */
    public function checkEmail()
    {
        if (isset($_POST)) {
            $email = input('email');
            $user = new User();
            $result = $user->userEmailIsExist($email);
            if ($result) {
                return json(['valid' => false]);
            }
            return json(['valid' => true]);

        }
    }


    /**
     * @param Request $request
     * 邮箱激活
     */
    public function activation(Request $request)
    {
        $username = $request->param('username');
        $pwd = urlsafe_b64decode($request->param('pwd'));
        if (!empty($username) && !empty($pwd)) {
            $user = new User();
            $result = $user->userActivation($username, $pwd);
            if ($result !== false) {
                $this->redirect('/login/index');
            }
        }
    }

    /**
     * @return \think\response\Json
     * 发送短信验证码
     */
    public function sendMessage()
    {
        if (\request()->isPost()) {
            $phone = input('phone');
            $section = input('section');
            if (!$this->isMobile($phone)) {
                return json(['resp_code' => '1', 'msg' => \think\lang::get('phone_rule')]);
            }
            if (!$this->checkExpire($phone)) {
                return json(['resp_code' => '2', 'msg' => \think\lang::get('short_message_reg')]);
            }

            $code = $this->random();
            $this->redis->set('user:' . $phone, $code);
            $this->redis->setex('user:' . $phone, 300, $code);
            $message = new \ShortMessage();
            $result = $message->sendSms('00' . $section . $phone, $code);
            if ($result->Message == 'OK' && $result->Code == 'OK') {
                return json(['resp_code' => '0', 'msg' => \think\lang::get('short_message_success')]);
            }
        }
    }

    /**
     * @return string
     * 返回随机验证码
     */
    private function random()
    {
        $length = 6;
        $char = '0123456789';
        $code = '';
        while (strlen($code) < $length) {
            //截取字符串长度
            $code .= substr($char, (mt_rand() % strlen($char)), 1);
        }
        return $code;
    }


    /**
     * @param $mobile
     * @return bool
     * 验证手机号码格式
     */
    private function isMobile($mobile)
    {
        if (preg_match('/^((13[0-9])|(14[5|7])|(15([0-3]|[5-9]))|(18[0,5-9])|(17[0,9])|(19[0,9]))\d{8}$/', $mobile))
            return true;
        return false;
    }

    /**
     * @param $mobile
     * @param $vc
     * @return bool
     * 校对输入验证码
     */
    public function checkVerifyCode()
    {
        if ($this->redis->get('user:' . input('phone')) === input('code')) {
            return json(['valid' => true]);
        }
        return json(['valid' => false]);
    }

    /**
     * @param $phone
     * @return bool
     * 1分钟内最多发一条，用分钟和手机号为key:min:201701041750:13888888888
     * 一天内最多10条，用日期和手机号号为key:day:20170104:13888888888
     * 这样按分钟生成的key比较多，可以把手机号对应的分钟放`set`内
     */
    function checkExpire($phone)
    {
        if ($this->redis->exists('min:' . date('YmdHi') . ':' . $phone) || $this->redis->get('day:' . date('YmdHi') . ':' . $phone) > 10) {
            return false;
        }
        $this->redis->set('min:' . date('YmdHi') . ':' . $phone, 1);
        $this->redis->incr('day:' . date('Ymd') . ':' . $phone);
        return true;
    }

}
