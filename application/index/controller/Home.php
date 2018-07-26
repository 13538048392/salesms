<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/25
 * Time: 18:29
 */

namespace app\index\controller;


use app\index\model\UserInfo;
use think\Controller;
use think\Request;

class Home extends Controller
{
    public function index(Request $request)
    {
        if (isset($request)) {
            $id = $request->param('userid');
            $userInfo = new UserInfo();
            $data = $userInfo->getUserInfoById($id);
            return view('/home', $data->toArray());
        }
    }
    public function index2()
    {
        return view('/home2');
    }

    public function prifile()
    {
        return view('profile');
    }

    public function updateProfile()
    {

    }

}