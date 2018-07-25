<?php
namespace app\admin\controller;

use think\Controller;
use app\admin\model\User as UserModel;
use think\Session;

class User extends Common
{
    public function memberlst()
    {
        //会员列表
        $user_model = new UserModel();
        $member = $user_model->getMemberList();
        // dump($member);
        return view('member_lst',['member'=>$member,'count'=>count($member)]);
    }


    public function isStop(){
        //停用
        $id = input('post.id');
        $res = UserModel::where('user_id',$id)->update(['status'=>0]);
        if ($res) {
            return json(['status'=>'200']);
        }
        else{
            return json(['status'=>'0']);
        }
    }

    public function isUse(){
        //启用
        $id = input('post.id');
        $res = UserModel::where('user_id',$id)->update(['status'=>1]);
        if ($res) {
            return json(['status'=>'200']);
        }
        else{
            return json(['status'=>'0']);
        }
    }

    


}
