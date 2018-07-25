<?php
namespace app\index\controller;

use think\Controller;

class Index extends Controller
{
    public function index()
    {
        return view('/index');
    }
    public function firstpage()
    {
        return view('/firstpage');
    }
    public function personal()
    {
        return view('/personal');
    }
    public function channel(){
        return view('/channel');
    }
    public function register()
    {
        return "this is register method";
    }
    public function login()
    {
        return "this is login method";
    }
}
