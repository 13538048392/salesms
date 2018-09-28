<?php
namespace app\index\controller;

use app\index\model\DocUserInfo as DocUserInfoModel;
use think\Config;
use think\Loader;

// use app\common\Base;

Loader::import('QueryingCode', ROOT_PATH . 'application/entend/QueryingCode.php');
class Api
{
    public function userRegister()
    {
        //用户注册
        $data = input('put.');
        //获取参数
        $token = Config::get('api_token');
        //token
        $field = array('firstName', 'lastName', 'contactPhone', 'referralCode', 'channelId');
        if (!isset($data['api_token'])) {
            return json(['code' => 2, 'msg' => 'Token does not exist.']);
        }
        if ($data['api_token'] != $token) {
            return json(['code' => 3, 'msg' => 'Token error']);
        }
        if (!isset($data['user']['id'])) {
            return json(['code' => 4, 'msg' => 'data in wrong format']);
        }

        foreach ($data['user']['profile'] as $k => $v) {
            if (!in_array($k, $field)) {
                return json(['code' => 4, 'msg' => 'data in wrong format']);
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
            return json(['code' => 5, 'msg' => 'data is already there. Please do not repeat it.']);
        }
        $res = DocUserInfoModel::insert($insert);
        if ($res) {
            return json(['code' => 0, 'msg' => 'success']);
        } else {
            return json(['code' => 1, 'msg' => 'fail']);
        }

    }

    public function getUsageHistory()
    {

        $referral = '38';
        $startDate = '2018-07-01';
        $endDate = '2019-07-01';
        date_default_timezone_set('UTC');
        $timeStamp = date('Y-m-d\TH', time());
        $key = 'vGaPb2eu1b9dtfGMJ8';
        $signature = $this->getSignature($timeStamp, $key);
        $url = "https://app.kooa.vip/usage?referralCode={$referral}&startDate={$startDate}&endDate={$endDate}&secret={$signature}";
        $result = $this->httpGet($url);
        return $result;
    }

    public function getECommerce($user_id, $startDate, $endDate)
    {
        date_default_timezone_set('UTC');
        $timeStamp = date('Y-m-d\TH', time());
        $key = 'GVz7V95yI0BalgPZFv';
        $signature = $this->getSignature($timeStamp, $key);
        $url = "http://ec.kooa.ai/eCommerce/API/token/index.php/API/user/usages?userid={$user_id}&startdate={$startDate}&enddate={$endDate}&secret={$signature}";
        $result = $this->httpGet($url);
        return $result;
//            return $url;
    }

    public function httpGet($url)
    {
        $curl = curl_init();
        //需要请求的是哪个地址
        curl_setopt($curl, CURLOPT_URL, $url);
        //表示把请求的数据已文件流的方式输出到变量中
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //开启 TLS False Start （一种 TLS 握手优化方式）php 7.0.7 有效
        curl_setopt($curl, CURLOPT_SSL_FALSESTART, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    public function getSignature($timeStamp, $key)
    {
        return $signature = sha1($timeStamp . $key);
    }

    public function createQrCode()
    {
        //生成二维码
        $code = new \QueryingCode();
        $input = input('get.');
        $token = Config::get('api_token');
        //token
        if (!isset($input['api_token'])) {
            return json(['code' => 2, 'msg' => 'Token does not exist.']);
        }
        if ($input['api_token'] != $token) {
            return json(['code' => 3, 'msg' => 'Token error']);
        }
        if (!isset($input['url'])) {
            return json(['code' => 4, 'msg' => 'data in wrong format']);
        }
        if (!isset($input['logo_url'])) {
            return json(['code' => 4, 'msg' => 'data in wrong format']);
        }

        try {
            $logo_res = file_get_contents($input['logo_url']);
        } catch (\Exception $e) {
            // echo "error";
            $logo_res = '';

        }
        //异常处理

        if (!$logo_res) {
            return json(['code' => 5, 'msg' => 'logo does not exist']);
        }

        $qr_string = $code->createQrCode($input['url'], $input['logo_url']);
        if ($qr_string) {
            return json(['code' => 0, 'msg' => 'success', 'qr_code_url' => $qr_string]);
        } else {
            return json(['code' => 1, 'msg' => 'fail']);

        }
        // echo $qr_string;
    }

    public function test()
    {
        //测试

        //       try{
        //     file_get_contents(input('get.url'));
        // }catch($e){
        //     echo $e->getMessage();
        // }

        try {
            $res = file_get_contents(input('get.url'));
        } catch (\Exception $e) {
            echo "error";
            $res = '';

        }
        dump($res);

    }
}
