<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/25
 * Time: 19:25
 */

namespace app\index\controller;

use think\Loader;
use app\admin\model\Role;
use app\admin\model\Url;
use think\Controller;
use think\Db;
use think\Request;

Loader::import('QueryingCode', ROOT_PATH . 'application/entend/QueryingCode.php');

use app\common\Base;

class Channel extends Base
{
    public function index(Request $request)
    {
        $userid = $request->param('userid');
        $channel = Db::name('channel')->where(['user_id' => $userid])->select();
        foreach ($channel as $key => $value) {
            $data = Db::name('url')
                ->alias('b')
                ->join('channel a', 'a.id=b.channel_id')
                ->join('role c', 'b.role_id=c.id')
                ->field('b.url_code,c.role_name')
                ->where('b.channel_id', $value['id'])
                ->select();
            $channel[$key]['url_code'] = $data;
        }
        return view('/channel', ['data' => $channel]);
    }


    public function test()
    {

        $code = new \QueryingCode();
        $input = input('get.url');
        $pic_name =input('pic_name');
        $type = input('type');
        // dump($pic_name);exit;
        $logoPath = ROOT_PATH.'public/static/images/logo.jpg';
        $qr_code = $code->createQrCode($input,$logoPath,'location');
        // dump($qr_code);exit;
        if ($type == 'sales') {
            $bg = ROOT_PATH.'public/static/images/sales_qrcode_bg.jpg';
        }
        else{
            $bg = ROOT_PATH.'public/static/images/doctor_qrcode_bg.jpg';
        }

        $bigImg = imagecreatefromstring(file_get_contents($bg));
        $qCodeImg = imagecreatefromstring(file_get_contents($qr_code));
         
        list($qCodeWidth, $qCodeHight, $qCodeType) = getimagesize($qr_code);
         
        imagecopymerge($bigImg, $qCodeImg, 85, 265, 0, 0, $qCodeWidth, $qCodeHight, 100);
         
        list($bigWidth, $bigHight, $bigType) = getimagesize($bg);
         
        imagejpeg($bigImg,'qr_code/temp_code.png');

        $filename = 'qr_code/temp_code.png';
        // 使用basename函数可以获得文件的名称而不是路径信息，保护了服务器的目录安全性
        // header("content-disposition:attachment;filename=".$filename);
        // header("content-length:".filesize($filename));
        // readfile($filename);

        $file=fopen($filename,"r");
        // header('Content-Description: File Transfer');
        header("Content-Type: application/octet-stream");
        header("Accept-Ranges: bytes");
        header("Accept-Length: ".filesize($filename));
        header("Content-Disposition: attachment; filename=$pic_name".'.png');
        
        echo fread($file,filesize($filename));
        fclose($file);

        //删除临时图片
        unlink($filename);
        unlink($qr_code);



        // $filename = $_GET['filename'];
        // $filename = 'static/images/logo.jpg';
        // // 使用basename函数可以获得文件的名称而不是路径信息，保护了服务器的目录安全性
        // header("content-disposition:attachment;filename=".$filename);
        // header("content-length:".filesize($filename));
        // readfile($filename);
    }

    public function down()
    {
        $type      = 'ie,opera';
        $down      = input('f'); //获取文件参数
        $file_name = $down . '.png'; //获取文件名称
        $dir       = "static/images/"; //相对于网站根目录的下载目录路径
        $down_host = $_SERVER['HTTP_HOST'] . '/'; //当前域名
        $file_path = 'http://' . $down_host . $dir . $file_name;
        $mime      = 'application/force-download';
        header('Pragma: public'); // required
        header('Expires: 0'); // no cache
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Type: ' . $mime);
        if ($type == 'ie' || $type == 'opera') {

            header('Content-Disposition: attachment; filename=' . urlencode($file_name));
        } else {

            header('Content-Disposition: attachment; filename=' . $file_name);
        }
        header('Content-Transfer-Encoding: binary');
        header('Connection: close');
        file_get_contents($file_path); // push it out
        exit;
    }

    public function addChannel(Request $request)
    {
        if (isset($_POST)) {
            $userId = $request->param('userid');
            $channelName = input('channel_name');
            $channel = new \app\index\model\Channel();
            $num = $channel->getChannelNumById($userId);
            if ($num > 10) {
                //最多创建十个渠道
                return json(['resp_code' => 1, 'msg' => \think\lang::get('channel_check')]);
            }
            $find = $channel->where(['channel_name'=>$channelName,'user_id'=>$userId])->find();
            if ($find) {
                return json(['resp_code' => 2, 'msg' => \think\lang::get('channel_exists')]);
            }
            $channelId = $channel->addChannel($userId, $channelName);
            $arr_role = Db::name('admin_role')->field('role_id')->where(['user_id' => $userId])->select();
            foreach ($arr_role as $key => $value) {
                switch ($value['role_id']) {
                    //如果是角色是医生
                    case 2:
                        $url_doctor = getShortUrl("http://app.kooa.vip/signup?channelId=" . $channelId . "&referralCode=" . $userId);
                        $data = [
                            'channel_id' => $channelId,
                            'url_code' => $url_doctor,
                            'role_id' => $value['role_id']
                        ];
                        Db::name('url')->insert($data);
                        break;
                    //如果角色是销售员
                    case 3:
                        $url_sale = getShortUrl("http://" . $_SERVER['SERVER_NAME'] . "/register/index/id/" . $channelId . "/role_id/" . $value['role_id']);
                        $url_doctor = getShortUrl("http://app.kooa.vip/signup?channelId=" . $channelId . "&referralCode=" . $userId);
                        $data = [
                            [
                                'channel_id' => $channelId,
                                'url_code' => $url_sale,
                                'role_id' => $value['role_id']
                            ],
                            [
                                'channel_id' => $channelId,
                                'url_code' => $url_doctor,
                                'role_id' => 2
                            ]
                        ];
                        Db::name('url')->insertAll($data);
                        break;
                }
            }
            return json(['resp_code' => 0, 'msg' => \think\lang::get('add_success')]);
        }
    }

    public function deleteChannel(Request $request)
    {
        if ($request) {
            $channelId = $request->param('channel_id');
            $channel = new \app\index\model\Channel();
            $result = $channel->deleteChannel($channelId);
            if ($result && $result !== false) {
                return json(['resp_code' => 0, 'msg' => 'true']);
            }
        }
    }

    public function changeStatus()
    {
        $status=input('status');
        $id=input('id');
        $channel = new \app\index\model\Channel();
        $result=$channel->changeStatus($status,$id);
        if($result)
        {
            return json(['resp_code' => 0, 'msg' => '更新成功']);
        }
    }

    public function QrCode()
    {
        $url= urldecode(input('url_code'));
        $logoPath=ROOT_PATH.'public/static/images/logo.jpg';
        $code = new \QueryingCode();
        $code->makeQueryingCode($url,$logoPath);
    }
}
