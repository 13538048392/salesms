<?php
use app\admin\model\SalesChannel as SalesChannelModel;
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
        $uid = '';//用户id
        $url = '';//根据渠道生成邀请url
        if ($data['channel_name'] == '') {
            return json(['msg'=>'渠道不能为空','status'=>1]);
        }
        if (isset($data['user_id'])) {
            $count = SalesChannelModel::where('user_id',$data['user_id'])->count();
        }
        // if (isset($data['admin_id'])) {
        //     $count = SalesChannelModel::where('admin_id',$data['admin_id'])->count();
        // }
        
        if ($count == 10) {
            return json(['msg'=>'最多只能添加10个渠道','status'=>3]);
        }
        // $insert_data['channel_name'] = $channel;
        // $insert_data['admin_id'] = Session::get('uid');
        $find = SalesChannelModel::where($data)->find();
        if ($find) {
            return json(['msg'=>'渠道已经存在','status'=>2]);
        }
        $channel_id = SalesChannelModel::insertGetId($data);
        
        if ($channel_id) {
            if (isset($data['user_id'])) {
                $uid = $data['user_id'];
                $url = $_SERVER['SERVER_NAME']."/register/index/channelid/$channel_id";
            }
            // if (isset($data['admin_id'])) {
            //     $uid = $data['admin_id'];
            //     $url = $_SERVER['SERVER_NAME']."/register/index/adminid/$uid/channelid/$channel_id";
            // }
            
            $update_url = SalesChannelModel::where('id',$channel_id)->update(['url_code'=>$url]);
            if ($update_url) {
                return json(['msg'=>'添加成功，生成url!',
                         'status'=>200,
                         'url'=>$url]);
            }
            else{
                return json(['msg'=>'生成url失败','status'=>4]);
            }
            
        }
        else{
            return json(['msg'=>'添加失败！','status'=>0]);
        }
        

    }