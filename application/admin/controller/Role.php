<?php
namespace app\admin\controller;

use think\Controller;

class Role extends Controller
{
    public function lst()
    {
        $data =model('Role')->lst();
        $this->assign('data',$data);
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
//            halt($data);
            $id=model('Role')->edit($data);
            return json("ok");
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
            return json("ok");
        }else{
            $data =model('privilege')->getTree();
            $this->assign('data',$data);
//        halt($data);
            return view();
        }

    }
}
