<?php
use app\admin\model\SalesChannel as SalesChannelModel;
use think\Session;
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
function doAddChannel($channel=''){
	if ($channel == '') {
            return json(['msg'=>'渠道不能为空','status'=>1]);
        }
        $insert_data['channel_name'] = $channel;
        $insert_data['user_id'] = Session::get('uid');
        $find = SalesChannelModel::where($insert_data)->find();
        if ($find) {
            return json(['msg'=>'渠道已经存在','status'=>2]);
        }
        $channel_id = SalesChannelModel::insertGetId($insert_data);
        if ($channel_id) {
            $url = $_SERVER['SERVER_NAME'].'/index.php/index/register?userid='.base64_encode(Session::get('uid')).'&&channelid='.base64_encode($channel_id);
            return json(['msg'=>'添加成功，生成url!',
                         'status'=>200,
                         'url'=>$url]);
        }
        else{
            return json(['msg'=>'添加失败！','status'=>0]);
        }
}
