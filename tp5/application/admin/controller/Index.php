<?php
namespace app\admin\controller;

use think\Controller;

class Index extends Controller
{
    public function index()
    {
      return view();
    }
    public function welcome(){

        return $this->fetch();
    }
}