<?php
namespace app\admin\controller;


use app\common\Base;
use think\Controller;
use think\Session;


class Common extends Base
{
    public function __construct(){
        parent::__construct();

        $uid = Session::get('uid');
        if (!isset($uid)) {
            $this->redirect(url('admin/Login/login'));
        }
        if($uid==1){
            return;
        }
         $data =model('admin')->chkPri($uid);
         if($data['has']==0){
            echo "<h1>您无权访问</h1>";exit;
 //            $this->redirect(url('admin/Login/login'));
         }


    }

}
