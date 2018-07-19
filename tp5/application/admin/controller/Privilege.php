<?php
namespace app\admin\controller;

use think\Controller;

class Privilege extends Controller
{
    public function lst()
    {

        return view();
    }
    public function edit()
    {

        return view();
    }
    public function add()
    {
        $parentData =model('privilege')->getTree();
        $this->assign('parentData',$parentData);
        return $this->fetch('add2');

    }
}
