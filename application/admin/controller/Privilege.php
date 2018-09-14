<?php
namespace app\admin\controller;

use think\Controller;

class Privilege extends Common
{
    public function lst()
    {
        $model =model('Privilege');

        $data = $model->getTree();
        $count = count($data);
        $this->assign('data',$data);
        $this->assign('count',$count);
        return view();
    }
    public function edit()
    {
        $priModel = model('Privilege');
        if (request()->isPost()){

            $data =input('post.');
            parse_str($data['str'],$data);
            $validate =validate('Privilege');
            if (!$validate->check($data)){
                $error =$validate->getError();

                return json("$error");
//                $this->error($validate->getError());
            }
            try{
                $id =$priModel->edit($data);

            }catch(\Exception $e){
                $error =$e->getMessage();

                return json("$error");

            }

            if ($id>=0){
                $count =\app\admin\model\Privilege::count();
                $data = $priModel->getTree();
                $this->assign('data',$data);
                $this->assign('count',$count);
                return $this->fetch('ajaxlst');

            }else{
                return json("系统正忙，请稍后再修改");
//                $this->error('error');
            }
        }else{
            $parentData =$priModel->getTree();
            $this->assign('parentData',$parentData);
            $priIdData = $priModel->getOne(input('get.id'));

            $this->assign('priIdData',$priIdData);
            return $this->fetch();
        }
    }
    public function add()
    {
        if (request()->isPost()){
            $data =input('post.');
            parse_str($data['str'],$data);
            $validate =validate('Privilege');
            if (!$validate->check($data)){
//                $this->error($validate->getError());
                $error =$validate->getError();
                return json("$error");
            }
            try{
                $id =model('Privilege')->add($data);
            }catch(\Exception $e){
                $error =$e->getMessage();

                return json("$error");
            }
            if ($id){
                $data = model("Privilege")->getTree();
                $count =\app\admin\model\Privilege::count();
                $this->assign('data',$data);
                $this->assign('count',$count);
                return $this->fetch('ajaxlst');

            }else{
                return json("系统正忙，请稍后再新增");
            }
        }else{
            $parentData =model('privilege')->getTree();
            $this->assign('parentData',$parentData);
            return $this->fetch('add');
        }


    }
    public function del(){
        $id = input('post.id');
//        $model =model('Privilege');
        $res['msg'] = \app\admin\model\Privilege::destroy($id);
        $data = model("Privilege")->getTree();
        $count =\app\admin\model\Privilege::count();
        $this->assign('data',$data);
        $this->assign('count',$count);
        return $this->fetch('ajaxlst');
    }



}
