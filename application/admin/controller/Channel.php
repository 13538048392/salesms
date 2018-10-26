<?php
namespace app\admin\controller;

use think\Controller;
use think\Session;
use app\admin\model\SalesRole as SalesRoleModel;
use app\admin\model\SalesAdminRole as SalesAdminRoleModel;
use app\admin\model\SalesChannel as SalesChannelModel;
use app\admin\model\Url as UrlModel;

class Channel extends Common
{
        public function lst(){
            //渠道列表
            $channel = SalesChannelModel::where('user_id',Session::get('uid'))->select()->toArray();
            $url_model = new UrlModel();
            $data = $url_model->getUrl($channel);
            return view('lst',['channel'=>$data]);
        }

        public function addChannel(){
        //添加渠道
        if (request()->isPost()) {
            //执行添加渠道
            $where['id'] = array('in',input('post.role'));
            $role = SalesRoleModel::where($where)->field('id,role_name')->select()->toArray();
            //获取需要添加的角色
            $data['role'] = $role;
            $data['channel_name'] = input('post.channel');
            $data['user_id'] = Session::get('uid');
            return doAddChannel($data);
        }
        else{
            $role = SalesRoleModel::where('type',1)->field('id,role_name')->select()->toArray();
            return view('add_channel',['role'=>$role]);
        }
        
    }
    public function editChannel(){
        //编辑渠道
        if (request()->isPost()) {
            //执行添加渠道
            $where['id'] = array('in',input('post.role'));
            $role = SalesRoleModel::where($where)->field('id,role_name')->select()->toArray();
            //获取需要添加的角色
            $data['role'] = $role;
            $data['channel_name'] = input('post.channel');
            $data['user_id'] = Session::get('uid');

            return doAddChannel($data);
        }
        else{
            $disabled = UrlModel::where('channel_id',input('get.id'))->field('role_id')->select()->toArray();
            $disabled_role = array_column($disabled, 'role_id');
            $role = SalesRoleModel::where('type',1)->field('id,role_name')->select()->toArray();
            $channel = SalesChannelModel::where('id',input('get.id'))->find();
            return view('edit_channel',['role'=>$role,
                                        'disabled_role'=>$disabled_role,
                                        'channel'=>$channel]);
                                }
    }

    public function delChannel(){
        //删除渠道
        $del_channel = SalesChannelModel::where('id',input('post.id'))->delete();
        $del_url = UrlModel::where('channel_id',input('post.id'))->delete();
        if ($del_channel && $del_url) {
            return json(['status'=>200,'msg'=>'删除成功']);
        }
        else{
            return json(['status'=>0,'msg'=>'删除失败']);
        }
        
    }
}
