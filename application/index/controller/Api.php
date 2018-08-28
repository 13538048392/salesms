<?php
namespace app\index\controller;
use app\index\model\User;
use think\Config;
use app\index\model\DocUserInfo as DocUserInfoModel;
use think\Loader;
// use app\common\Base;

Loader::import('QueryingCode', ROOT_PATH . 'application/entend/QueryingCode.php');
class Api 
{
    public function userRegister(){
    	//用户注册
        $data = input('put.');
        //获取参数
        $token = Config::get('api_token');
        //token
        $field = array('firstName','lastName','contactPhone','referralCode','channelId');
        if (!isset($data['api_token'])) {
        	return json(['code' => 2,'msg'=>'Token does not exist.']);
        }
        if ($data['api_token'] != $token) {
        	return json(['code' => 3,'msg'=>'Token error']);
        }
        if (!isset($data['user']['id'])) {
        	return json(['code' => 4,'msg'=>'data in wrong format']);
        }

        foreach ($data['user']['profile'] as $k => $v) {
        	if (!in_array($k, $field)) {
        		return json(['code' => 4,'msg'=>'data in wrong format']);
        	}
        }
        $insert['user_id'] = $data['user']['id'];
        $insert['firstName'] = $data['user']['profile']['firstName'];
        $insert['lastName'] = $data['user']['profile']['lastName'];
        $insert['contactPhone'] = $data['user']['profile']['contactPhone'];
        $insert['referralCode'] = $data['user']['profile']['referralCode'];
        $insert['channelId'] = $data['user']['profile']['channelId'];
        $find = DocUserInfoModel::where($insert)->find();
        if ($find) {
        	return json(['code' => 5,'msg' => 'data is already there. Please do not repeat it.']);
        }
        $res = DocUserInfoModel::insert($insert);
        if ($res) {
        	return json(['code' => 0,'msg'=>'success']);
        }
        else{
        	return json(['code' => 1,'msg'=>'fail']);
        }

    }

    public function createQrCode(){
    	//生成二维码
    	$code = new \QueryingCode();
    	$input = input('get.');
    	$token = Config::get('api_token');
        //token
        if (!isset($input['api_token'])) {
        	return json(['code' => 2,'msg'=>'Token does not exist.']);
        }
        if ($input['api_token'] != $token) {
        	return json(['code' => 3,'msg'=>'Token error']);
        }
        if (!isset($input['url'])) {
        	return json(['code' => 4,'msg'=>'data in wrong format']);
        }
        if (!isset($input['logo_url'])) {
        	return json(['code' => 4,'msg'=>'data in wrong format']);
        }
        $qr_string = $code->createQrCode($input['url'],$input['logo_url']);
        if ($qr_string) {
        	return json(['code' => 0,'msg' => 'success','qr_code_url' => $qr_string]);
        }
        else{
        	return json(['code' => 1,'msg' => 'fail']);

        }
        // echo $qr_string;
    }

}
