<?php
namespace app\admin\controller;

use think\Controller;

class Role extends Common
{
    public function lst()
    {
        $data =model('Role')->lst();
        $count =\app\admin\model\Role::count();
        $this->assign('data',$data);
        $this->assign('count',$count);
        return $this->fetch();

    }
    public function edit()
    {
        if (request()->isPost()){

            $data = input('post.');
            parse_str($data['str'],$data);
            $validate =validate('Role');

            if (!$validate->check($data)){
//                $this->error($validate->getError());
                $error =$validate->getError();
                return json("$error");
            }
            $id=model('Role')->edit($data);
            if ($id>=0){
                $data =model('Role')->lst();
                $count =\app\admin\model\Role::count();
                $this->assign('data',$data);
                $this->assign('count',$count);
                return $this->fetch('ajaxlst');
            }else{
                return json("系统正忙，请稍后再修改");
            }
        }else{
            $id =input('get.id');
            $result =model('role')->getOne($id);

            $onedata[] =$result[0]['id'];
            $onedata[] =$result[0]['role_name'];
            $onedata[]=array_column($result,'pri_id');
            $data =model('privilege')->getTree();
            $this->assign('data',$data);
            $this->assign('onedata',$onedata);

            return view();
        }

    }
    public function add()
    {
        if (request()->isPost()){
            $data = input('post.');
            parse_str($data['str'],$data);
            $validate =validate('Role');
            if (!$validate->check($data)){
//                $this->error($validate->getError());
                $error =$validate->getError();
                return json("$error");
            }

            $id=model('Role')->add($data);
            if ($id){
                $data =model('Role')->lst();
                $count =\app\admin\model\Role::count();
                $this->assign('data',$data);
                $this->assign('count',$count);
                return $this->fetch('ajaxlst');
            }else{
                return json('添加失败');
            }

        }else{
            $data =model('privilege')->getTree();
            $this->assign('data',$data);
//        halt($data);
            return view();
        }

    }
    public function del(){
        $id =input('post.id');
        if (model('role')->del($id)){
            return join(['msg'=>'删除成功']);
        }else{
            return join(['msg'=>'失败成功']);
        }


    }
}
