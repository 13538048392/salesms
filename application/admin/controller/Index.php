<?php
namespace app\admin\controller;

use app\common\Base;
use think\Controller;
use think\Session;

class Index extends Base
{
    public function __construct()
    {
        parent::__construct();
        $uid = Session::get('uid');

        if (!isset($uid)) {
            $this->redirect(url('admin/Login/login'));
        }
    }

    public function index()
    {   
      return view();
    }
    public function welcome(){
        return view();
    }
}
