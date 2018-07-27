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
        if (Session::has('user.username')) {
            $this->redirect('index/index');
//        } elseif (Cookie::has('user.username')) {
//            $username = Cookie::get('user.username');
//            $password = Cookie::get('user.password');
//            $user = new User();
//            $result = $user->vertifyCookie($username, $password);
//            if ($result && $result !== false) {
//                $this->redirect('index/index');
//            }
        } else {
            return view('/login');
        }
    }

    public function login()
    {
        if (isset($_POST)) {
            $username = input('username');
            $password = input('password');
            $user = new User();
            $result = $user->userLogin($username);
            if ($result && $result !== false) {
                if (password_verify($password, $result['pass'])) {
                    if ($result['active'] == 0) {
                        return json(['resp_code' => 3, 'msg' => 'Email is not verified']);
                    } else {
                        Session::set('user.username', $username);
                        Session::set('userid', $result['user_id']);
                        // Cookie::set('user,username', $result['user_id'], 604800);
                        // Cookie::set('user.password', $user['pass'], 604800);
                        return json(['resp_code' => 0, 'user_id' => $result['user_id']]);
                    }
                } else {
                    return json(['resp_code' => 2, 'msg' => 'password is unavailable']);
                }
            } else {
                return json(['resp_code' => 1, 'msg' => 'user is unavailable']);
            }
        }
    }

    public function testLogin()
    {
        $user = new User();
        $result = $user->userLogin('hannan');
        //$this->redirect('/index/firstpage')->params(['userid'=>$result['user_id']]);
        $this->redirect('/home/index/userid/' . $result['user_id']);
    }

    public function loginOut()
    {
        Session::clear();
        Session::destroy();
        $this->redirect('/index');
    }

    public function checkValidateCode()
    {
        if (isset($_POST)) {
            $verify_code = input('verify');
            if (!captcha_check($verify_code)) {
                // 校验失败
                echo json_encode(['valid' => false]);
            } else {
                echo json_encode(['valid' => true]);
            }
        }

    }
}