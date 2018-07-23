<?php
namespace app\admin\controller;

use think\Controller;
use app\admin\model\Admin as AdminModel;
use app\admin\model\SalesRole as SalesRoleModel;
use app\admin\model\SalesAdminRole as SalesAdminRoleModel;
use app\admin\model\SalesChannel as SalesChannelModel;
use think\Session;

class Admin extends Common
{
    public function lst()
    {
        $admin_model = new AdminModel();
        $data = $admin_model->getAdmin();
        $count = $admin_model->adminCount();
        // dump($data);
        return view('lst',['admin'=>$data,'count'=>$count]);
    }
    public function edit()
    {//管理员修改页面
        $id = input('get.id');
        $user = AdminModel::where('id',$id)
                            ->field('id,username')
                            ->find()
                            ->toArray();
        // dump($user);
        $role_id = SalesAdminRoleModel::where('admin_id',$id)->value('role_id');
        $role = SalesRoleModel::select()->toArray();
        return view('edit',['user'=>$user,'role'=>$role,'role_id'=>$role_id]);
    }
    public function doEdit(){
        //管理员修改页面执行
        if (input('post.newpassword') != '' || input('post.newpassword2') != '') {
            // return json(['msg'=>'密码不能为空！','status'=>3]);
            if (input('post.newpassword') != input('post.newpassword2')) {
                return json(['msg'=>'两次输入密码不一致！','status'=>1]);
            }
        }
        
            $newpassword = password_hash(input('post.newpassword'),PASSWORD_DEFAULT);
            $id = input('post.id');
            $res = AdminModel::where('id',$id)->update(['pass'=>$newpassword]);
            if (!$res) {
                return json(['msg'=>'修改失败！','status'=>2]);
            }
        
            $re = SalesAdminRoleModel::where('admin_id',$id)->update(['role_id'=>input('post.role')]);
            if ($re !== false) {
                return json(['msg'=>'修改成功！','status'=>200]);
            }
            else{
                return json(['msg'=>'角色修改失败!','status'=>4]);
                
            }
        

    }
    public function add()
    {
        //添加管理员
      $role = SalesRoleModel::where('id','<>',1)->select()->toArray();
      // dump($role);
      return view('add',['role'=>$role]);
    }

    public function doAdd(){
        //添加管理员执行
        if (input('post.password') != input('post.password2')) {
            return json(['msg'=>'两次输入密码不一致！','status'=>1]);
        }
        $insert_admin['pass'] = password_hash(input('post.password'),PASSWORD_DEFAULT);
        $insert_admin['username'] = input('post.admin');
        $res = AdminModel::where(['username'=>$insert_admin['username']])->find();
        if ($res) {
            return json(['msg'=>'管理员已经存在，不可重复创建！','status'=>2]);
        }
        
        $insert_admin_role['admin_id'] = AdminModel::insertGetId($insert_admin);
        if ($insert_admin_role['admin_id'] != 0) {
            $insert_admin_role['role_id'] = input('post.role');

            $re = SalesAdminRoleModel::insert($insert_admin_role);

            if ($re) {
                return json(['msg'=>'创建成功！','status'=>200]);
            }
            else{
                return json(['msg'=>'创建失败，服务器错误！','status'=>4]);
            }
        }
        else{
            return json(['msg'=>'创建失败，服务器错误！','status'=>3]);
        }
        
    }

    public function isStop(){
        //停用
        $id = input('post.id');
        $res = AdminModel::where('id',$id)->update(['status'=>0]);
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
        $res = AdminModel::where('id',$id)->update(['status'=>1]);
        if ($res) {
            return json(['status'=>'200']);
        }
        else{
            return json(['status'=>'0']);
        }
    }

    public function addChannel(){
        //添加渠道
        return view();
    }

    public function doAddChannel(){
        //执行添加渠道
        $channel = input('post.channel');
        // if ($channel == '') {
        //     return json(['msg'=>'渠道不能为空','status'=>1]);
        // }
        // $insert_data['channel_name'] = $channel;
        // $insert_data['user_id'] = Session::get('uid');
        // $find = SalesChannelModel::where($insert_data)->find();
        // if ($find) {
        //     return json(['msg'=>'渠道已经存在','status'=>2]);
        // }
        // $channel_id = SalesChannelModel::insertGetId($insert_data);
        // if ($channel_id) {
        //     $url = $_SERVER['SERVER_NAME'].'index.php/index/register?userid='.Session::get('uid').'&&channelid='.$channel_id;
        //     return json(['msg'=>'添加成功，生成url!',
        //                  'status'=>200,
        //                  'url'=>$url]);
        // }
        // else{
        //     return json(['msg'=>'添加失败！','status'=>0]);
        // }
        return doAddChannel($channel);

    }


}
