<?php
namespace app\admin\controller;

use think\Controller;
use app\admin\model\Admin as AdminModel;
use think\Session;

class Login extends Controller
{
    public function login(){
    	//登录页
    	return view();
    }
    public function doLogin(){
    	//执行登录
    	$verify_code = input('post.verify_code');
    	$username = input('post.username');
    	$pass = input('post.pass');
    	if (empty($username)) {
    		return json(['msg'=>'用户名不能为空！','status'=>1]);
    	}
    	if (empty($pass)) {
    		return json(['msg'=>'密码不能为空！','status'=>2]);
    	}
    	if (empty($verify_code)) {
    		return json(['msg'=>'验证码不能为空！','status'=>3]);
    	}
    	$admin_model = new AdminModel();
        $check_user = $admin_model->where(['user_name'=>$username,'status'=>1])->find();
        if (!$check_user) {
        	return json(['msg'=>'用户名不存在！','status'=>5]);
        }
        //验证密码
        $res = password_verify($pass,$check_user['pass']);
        if (!$res) {
        	return json(['msg'=>'密码错误！','status'=>6]);
        }
        else if(!captcha_check($verify_code)) {
            // 校验失败
            return json(['msg'=>'验证码错误！','status'=>4]);
        }else{
        	$where = 'a.id='.$check_user['id'];
        	$data = $admin_model->getAdmin($where);
        	// dump($data);
        	Session::set('admin_name',$data[$check_user['id']]['user_name']);
        	Session::set('uid',$data[$check_user['id']]['id']);
            Session::set('role_name',$data[$check_user['id']]['role_name']);
        	return json(['msg'=>'正在登陆！','status'=>200]);
        }
    }
    public function loginOut(){
    	//退出登录
    	Session::clear();
    	$this->redirect(url('admin/Login/login'));
    }
}
