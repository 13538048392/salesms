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

    public function enable(){
        //停用
        $id = input('post.id');
        $type = input('post.type');
        $res = '';
        if ($type == 1) {
            $res = UserModel::where('id',$id)->update(['status'=>1]);
        }
        if ($type == 0) {
            $res = UserModel::where('id',$id)->update(['status'=>0]);
        }
        if ($res) {
            return json(['status'=>'200']);
        }
        else{
            return json(['status'=>'0']);
        }
    }

}
