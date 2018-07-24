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
        $count = count($data);
        // dump($data);
        return view('lst',['admin'=>$data,'count'=>$count]);
    }
    public function edit()
    {//管理员修改页面
        $id = input('get.id');
        $admin_model = new AdminModel();
        $user = $admin_model->getAdmin('a.id='.$id);
        // $user = AdminModel::where('id',$id)
        //                     ->field('id,username')
        //                     ->find()
        //                     ->toArray();
        // dump($user);
        //检查超级管理员
        $check_root = strpos($user[$id]['role_name'],'超级管理员');
        if ($check_root === false) {
            $check_root = '';
        }
        else{
            $check_root = 'none';
        }
        $role_ids = SalesAdminRoleModel::where('admin_id',$id)
                                    ->field('role_id')
                                    ->select()
                                    ->toArray();

        $role_array = array();
        //角色id集合
        foreach ($role_ids as $key => $value) {
            array_push($role_array,$value['role_id']);
        }
        // dump($role_ids);
        //角色选项框集合
        $role = SalesRoleModel::where('role_name','<>','超级管理员')->select()->toArray();
        return view('edit',['user'=>$user,
                            'role'=>$role,
                            'role_array'=>$role_array,
                            'uid'=>$id,
                            'is_root'=>$check_root]);
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
            
            $role_ids = explode(',',input('post.role'));
            foreach ($role_ids as $key => $value) {
                $insert_admin_role[$key]['admin_id'] = $id;
                $insert_admin_role[$key]['role_id'] = $value;
            }
            $del_res = SalesAdminRoleModel::where('admin_id',$id)->delete();
            $re = SalesAdminRoleModel::insertAll($insert_admin_role);
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
      $role = SalesRoleModel::where('role_name','<>','超级管理员')->select()->toArray();
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
        
        $admin_id = AdminModel::insertGetId($insert_admin);
        if ($admin_id != 0) {
            $role_ids = explode(',',input('post.role'));
            foreach ($role_ids as $key => $value) {
                $insert_admin_role[$key]['admin_id'] = $admin_id;
                $insert_admin_role[$key]['role_id'] = $value;
            }

            $re = SalesAdminRoleModel::insertAll($insert_admin_role);

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
            $url = $url = $_SERVER['SERVER_NAME'].'/index.php/index/register?userid='.base64_encode(Session::get('uid')).'&&channelid='.base64_encode($channel_id);
            return json(['msg'=>'添加成功，生成url!',
                         'status'=>200,
                         'url'=>$url]);
        }
        else{
            return json(['msg'=>'添加失败！','status'=>0]);
        }
        

    }


}
