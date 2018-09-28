<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/25
 * Time: 19:25
 */

namespace app\index\controller;

use think\Loader;
use think\Controller;
use think\Db;
use think\Request;
use app\index\model\UserInfo;
use app\index\model\User;

Loader::import('QueryingCode', ROOT_PATH . 'application/entend/QueryingCode.php');

use app\common\Base;

class Team extends Base
{
    public function index(){
        //团队
        $user_id = session('userid');
        $user_info = UserInfo::where('user_id',$user_id)->find();
        //当前主账号
        $cus_id = User::where('id',$user_id)->value('cus_id');
        $cus_info = UserInfo::where('user_id',$cus_id)->find();
        // dump($cus_id);
        return view('/team',[ 'data' => $user_info,'cus_data' => $cus_info]);
    }

    public function editAgent(){
        //修改主账号用户资料
        $user_id = session('userid');
        if(request()->isPost()){
            $update_data = input('post.');
            $res = UserInfo::where('user_id',$user_id)->update($update_data);
            if ($res) {
                return json(['status' => 200, 'msg' => '修改成功']);
            }
            else{
                return json(['status' => 0 , 'msg' => '修改失败']);
            }

        }
        else{
            
            $data = UserInfo::where('user_id',$user_id)->find();

            return view('/update_agent',['data' => $data]);
        }
        
    }

}
