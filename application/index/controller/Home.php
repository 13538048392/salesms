<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/25
 * Time: 18:29
 */

namespace app\index\controller;


use app\index\model\UserInfo;
use think\Controller;
use think\Request;
use app\common\Base;

class Home extends Base
{
    public function index(Request $request)
    {
        if (isset($request)) {
            $arr=$request->param();
            if(isset($arr['userid']))
            {
                $id = $request->param('userid');
                $userInfo = new UserInfo();
                $data = $userInfo->getUserInfoById($id);
                return view('/home', ['data'=>$data]);
            }
        }
    }

    public function updateProfile(Request $request)
    {
        if(isset($_POST)){
            $userid=$request->param('userid');
            $firstName=input('first_name');
            $lastName=input('last_name');
            $address=input('address');
            $wechat=input('wechat');
            $gender=input('gender');
            $userinfo=new UserInfo();
            if($userinfo->idIsExist($userid)){
                $userinfo->updateUserInfo($userid,$firstName,$lastName,$address,$wechat,$gender);
                return json(['resp_code'=>1,'msg'=>'update suceess']);
            }else{
                $userinfo->addUserInfo($userid,$firstName,$lastName,$address,$wechat,$gender);
                return json(['resp_code'=>0,'msg'=>'add success']);
            }
        }
    }
}