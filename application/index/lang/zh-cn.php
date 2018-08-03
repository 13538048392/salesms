<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/27
 * Time: 18:05
 */
return [
//    '按钮1'=>'中文',//表示模板内使用{:lang('按钮1')}获得的字符在中文状态下显示为中文按钮1
//    '按钮2'=>'英文',
    'test'=>'测试',
    'name'=>'名称',
/*
	Login控制器以及相应模块的语言切换
*/
    'user' => '用户',
    'login' => '登录',
    'register' => '注册',
    'username' => '用户名',
    'email' => '邮箱',
    'phone'	=> '手机号',
    'password' => '密码',
    'password2' => '确认密码',
    'forgot' => '忘记密码?',
    'user_not_null' => '用户名不能为空',
    'pass_not_null' => '密码不能为空',
    'pass2_not_null' => '确认密码不能为空',
    'reset_pass' => '请重置您的密码',
    'reset_pass2' => '请确认您的重置密码',
    'email_not_null' => '邮箱不能为空',
    'phone_not_null' => '手机不能为空',
    'verify_code' => '验证码',
    'send_email_code' => '发送邮件',
    'commit' => '提交',
    'forgot_info' => '请输入您的邮箱发送验证码去进行重置密码',
    'check_email_user' => '用户名和邮箱不匹配',
    'send_email_again' => '重新发送',
    'verify_code_error' => '验证码错误',
    'two_pass_differ' => '两次输入密码不一致',
    'pass_reset_success' => '密码重置成功',
    'pass_reset_fail' => '密码重置失败',
    'send_email_success' => '发送邮件成功，请转到邮箱完成密码恢复操作。',
    'send_email_fail' => '发送邮件失败',
    'email_body_one' => '您的验证码是',
    'email_body_two' => '有效期一个小时，请尽快把验证码输入到网页上完成找回密码操作，如果这条信息不是您本人操作请忽视！',
    'email_title' => '找回密码',
    'user_not_exist' => '用户名不存在',
    'user_not_activate' => '账号未激活，请到您的邮箱激活',
    'user_frozen' => '您的账号已被冻结',
    'user_frozen_seconds' => '秒后重试',
    'password_error' => '密码不正确，超过五次账号将被冻结十分钟',
    'verify_success' => '验证成功',

/*
	login end
*/

/*
	register控制器的语言转换
*/
	'inviting_link_not_exist' => '邀请链接不存在',
	'inviting_link_invalid' => '邀请链接无效',
	'unlawful_request' => '非法请求',
	'user_error' => '用户信息不正确',
	'register_fail' => '注册失败请重新注册',
	'register_title' => '获取激活码',
	'register_email_body' => '注册成功，您的激活码是：',
	'register_email_body2' => '请点击该地址激活您的用户',
	'register_success' => '注册成功，请到邮箱激活您的账号',
	'register_fail' => '邮件发送失败，请重新注册',
	
	'user_length' => '用户名长度必须在8到18位之间',
	'user_rule' => '用户名只能包含大写、小写、数字',
	'email_rule' => '邮箱地址格式有误',
	'user_exist' => '用户已存在',
	'email_exist' => '邮箱已使用',
	'phone_rule' => '电话号码格式不正确',
	'pass_length' => '密码长度必须在6到30之间',
	'user_both' => '不能和用户名相同',
	'pass_rule' => '密码由数字字母下划线和.组成',





];