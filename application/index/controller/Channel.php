<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/25
 * Time: 19:25
 */

namespace app\index\controller;


use think\Controller;

class Channel extends Controller
{
    public function index()
    {
        return view('/channel');
    }

    public  function addChannel()
    {
        return view('/addchannel');
    }
}