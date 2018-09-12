<?php
use app\admin\model\SalesChannel as SalesChannelModel;
use app\admin\model\Url as UrlModel;
use app\admin\model\Role as RoleModel;
use app\admin\model\ShortUrl as ShortUrlModel;
use think\Config;
//Loader::import('Mailer',ROOT_PATH . 'application/entend/Mailer.php');

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function urlsafe_b64encode($string) {
    //base64加密替换特殊字符
   $data = base64_encode($string);
   $data = str_replace(array('+','/','='),array('-','_',''),$data);
   return $data;
 }

 function urlsafe_b64decode($string) {
    //base64解密替换特殊字符串
   $data = str_replace(array('-','_'),array('+','/'),$string);
   $mod4 = strlen($data) % 4;
   if ($mod4) {
       $data .= substr('====', $mod4);
   }
   return base64_decode($data);
 }

function doAddChannel($data){
        //执行添加渠道,
        //该方法有两个参数,channel渠道名,user_id用户id
        $count = '';//渠道数
        $insert_data = array();//写入url表的数据
        if ($data['channel_name'] == '') {
            return json(['msg'=>'渠道不能为空','status'=>1]);
        }
        if (isset($data['user_id'])) {
            $count = SalesChannelModel::where('user_id',$data['user_id'])->count();
        }

        if ($count == 10) {
            return json(['msg'=>'最多只能添加10个渠道','status'=>3]);
        }

        $find = SalesChannelModel::where(['channel_name'=>$data['channel_name'],'user_id'=>$data['user_id']])->find();
        if ($find) {
            //已存在渠道的时候进行url添加处理
            $url_num = UrlModel::where('channel_id',$find['id'])->count();
            $role_num = RoleModel::where('type',1)->count();

            if ($url_num == $role_num) {
                return json(['msg'=>'渠道已经存在','status'=>2]);
            }
            else{
                foreach ($data['role'] as $k => $v) {
                    $check_channel_role = UrlModel::where(['channel_id'=>$find['id'],'role_id'=>$v['id']])->find();
                        if ($check_channel_role) {
                            continue;
                        }
                        else if($v['id'] == 2){
                            //医生的url
                            $insert_data[$k]['channel_id'] = $find['id'];
                            $insert_data[$k]['role_id'] = $v['id'];
                            $insert_data[$k]['url_code'] = getShortUrl("http://app.kooa.vip/signup?channelId=$find[id]&referralCode=$data[user_id]"); 

                        }
                        else{
                            $insert_data[$k]['channel_id'] = $find['id'];
                            $insert_data[$k]['role_id'] = $v['id'];
                            $insert_data[$k]['url_code'] = getShortUrl('http://'.$_SERVER['SERVER_NAME']."/register/index/id/$find[id]/role_id/".$v['id']); 
                        }

                }

                $res = UrlModel::insertAll($insert_data);
            }
            
        }
        else{
            //没有渠道存在的时候进行url的处理
            $channel_id = SalesChannelModel::insertGetId(['channel_name'=>$data['channel_name'],'user_id'=>$data['user_id']]);
            //循环角色，有多少个角色就插入多少条数据，同时生成多少条url
            foreach ($data['role'] as $k => $v) {
                $check_channel_role = UrlModel::where(['channel_id'=>$channel_id,'role_id'=>$v['id']])->find();
                if ($check_channel_role) {
                    continue;
                }
                else if($v['id'] == 2){
                            //医生的url
                            $insert_data[$k]['channel_id'] = $channel_id;
                            $insert_data[$k]['role_id'] = $v['id'];
                            $insert_data[$k]['url_code'] = getShortUrl("http://app.kooa.vip/signup?channelId=$channel_id&referralCode=$data[user_id]"); 
                }
                else{
                    $insert_data[$k]['channel_id'] = $channel_id;
                    $insert_data[$k]['role_id'] = $v['id'];
                    $insert_data[$k]['url_code'] = getShortUrl('http://'.$_SERVER['SERVER_NAME']."/register/index/id/$channel_id/role_id/".$v['id']); 
                }
            }

            $res = UrlModel::insertAll($insert_data);
    }
        

        if ($res) {
            return json(['msg'=>'添加成功，生成url!',
                     'status'=>200,
                     'url'=>$insert_data,
                     'role'=>$data['role']]);
        }
        else{
            return json(['msg'=>'生成url失败','status'=>4]);

        }

    
}

    // function getShortUrl($url){
    //     //获取短链接
    //     $api_url = 'http://api.c7.gg/api.php?url=';
    //     //api接口地址
    //     $url = str_replace('&', '%26', $url);
    //     //字符串替换&为%26
    //     $get_url = $api_url.$url;
    //     $short_url = file_get_contents($get_url);
    //     // dump($short_url);exit;
    //     return $short_url;

    // }


    // function getShortUrl($url){
    //     //生成短连接
    //     $short_url = shorturl($url);
    //     // dump($short_url);exit;
    //     foreach ($short_url as $k => $v) {
    //        $find = ShortUrlModel::where('short_url',$v)->find();
    //        if (!$find) {
    //         //去重
    //            $data['short_url'] = $v;
    //            $data['url'] = $url;
    //            $data['create_time'] = time();
    //            $res = ShortUrlModel::insert($data);
    //            if ($res) {
    //                 return $v;
    //             }
    //             else{
    //                 return '';
    //             }
    //        }
    //     }
        
    // }

    function getShortUrl($url){
      //生成短连接
      $url = str_replace('&', '%26', $url);
      //字符串替换&为%26
      $short_url = "http://soc.kooa.vip/api/v2/action/shorten?key=d2ffa91d967c1424c992c2176df88c&url=".$url.'&is_secret=true';
      $short_url = file_get_contents($short_url);
      return $short_url;

    }

    function shorturl( $input ) 
    { 
        //加密算法
         $base32 = array (
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
            'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p',
            'q', 'r', 's', 't', 'u', 'v', 'w', 'x',
            'y', 'z', '0', '1', '2', '3', '4', '5'
            );
         
          $hex = md5($input);
          $hexLen = strlen($hex);
          $subHexLen = $hexLen / 8;
          $output = array();
         
          for ($i = 0; $i < $subHexLen; $i++) {
            $subHex = substr ($hex, $i * 8, 8);
            $int = 0x3FFFFFFF & (1*(bin2hex($subHex)));
            $out = '';
            // dump(0x3FFFFFFF & (1*(0x0e33af79)));
            // dump($int);
            for ($j = 0; $j < 6; $j++) {
              $val = 0x0000001F & $int;
              $out .= $base32[$val];
              $int = $int >> 5;

            }
         
            $output[] = $out;
          }
         
          return $output;
    }


    function sendEmail($password,$username,$email)
    {
        $password=urlsafe_b64encode($password);
        $url = url('index/register/activation','','',true);
        $url .= '/username/' . $username . '/pwd/' . $password;
        $strHtml = '<a href=' . $url . ' target="_blank">' . $url . '</a><br>';
        $subject = \think\lang::get('register_title');
        $body = \think\lang::get('register_email_body') . $strHtml . \think\lang::get('register_email_body2');
        $mail = new \Mailer();
        if ($mail->send($email,$subject,$body)) {
            //return '0';
            return json(['resp_code' => '0','msg' => \think\lang::get('register_success')]); //邮件发送成功
        } else {
            //return '3';
            return json(['resp_code' => '3','msg' => \think\lang::get('register_fail')]); //邮件发送失败
        }
    }
