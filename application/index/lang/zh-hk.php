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
//    '按钮3'=>'繁體',
    'test'=>'測試',
    'name'=>'名稱',

//login controller
    'user' => '用戶',
    'login' => '登錄',
    'register' => '註冊',
    'username' => '用戶名',
    'email' => '郵箱',
    'phone'	=> '手機號',
    'password' => '密碼',
    'password2' => '確認密碼',
    'forgot' => '忘記密碼?',
    'user_not_null' => '用戶名不能為空',
    'pass_not_null' => '密碼不能為空',
    'pass2_not_null' => '確認密碼不能為空',
    'email_not_null' => '郵箱不能為空',
    'phone_not_null' => '手機不能為空',
    'reset_pass' => '請重置您的密碼',
    'reset_pass2' => '請確認您的重置密碼',
    'verify_code' => '驗證碼',
    'send_email_code' => '發送郵件',
    'commit' => '提交',
    'forgot_info' => '請輸入您的郵箱發送驗證碼去進行重置密碼',
    'check_email_user' => '用戶名和郵箱不匹配',
    'send_email_again' => '重新發送',
    'verify_code_error' => '驗證碼錯誤',
    'two_pass_differ' => '兩次輸入密碼不一致',
    'pass_reset_success' => '密碼重置成功',
    'pass_reset_fail' => '密碼重置失敗',
    'send_email_success' => '發送郵件成功,請轉到郵箱完成密碼恢復操作。',
    'send_email_fail' => '發送郵件失敗',
    'email_body_one' => '您的驗證碼是',
    'email_body_two' => '有效期一個小時,請儘快把驗證碼輸入到網頁上完成找回密碼操作,如果這條資訊不是您本人操作請忽視！',
    'email_title' => '找回密碼',
    'user_not_exist' => '用戶名不存在',
	'user_not_activate' => '帳號未啟動,請到您的郵箱啟動',
	'user_frozen' => '您的帳號已被凍結',
	'user_frozen_seconds' => '秒後重試',
	'password_error' => '密碼不正確,超過五次帳號將被凍結十分鐘',
	'verify_success' => '驗證成功',

//end login controller 

	//register controller

	'inviting_link_not_exist' => '邀請連結不存在',
	'inviting_link_invalid' => '邀請連結無效',
	'unlawful_request' => '非法請求',
	'user_error' => '用戶資訊不正確',
	'register_fail' => '注册失敗請重新注册',
	'register_title' => '獲取啟動碼',
	'register_email_body' => '注册成功,您的啟動碼是',
	'register_email_body2' => '請點擊該地址啟動您的用戶',
	'register_success' => '注册成功,請到郵箱啟動您的帳號',
	'register_fail' => '郵件發送失敗,請重新注册',

	'user_length' => '用戶名長度必須在8到18比特之間',
	'user_rule' => '用戶名只能包含大寫、小寫、數位',
	'email_rule' => '郵箱地址格式有誤',
	'user_exist' => '用戶已存在',
	'email_exist' => '郵箱已使用',
	'phone_rule' => '電話號碼格式不正確',
	'pass_length' => '密碼長度必須在6到30之間',
	'user_both' => '不能和用戶名相同',
	'pass_rule' => '密碼由數位字母底線和.組成',

	//end register controller

];