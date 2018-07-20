<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/19
 * Time: 10:47
 */

namespace app\index\controller;


use think\Controller;
use think\Request;
use think\Validate;
class Register extends Controller
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        if(isset($_SESSION['username'])&&!empty($_SESSION['username'])){
        }
    }
    public function index()
    {
        return $this->view->fetch('/register');
    }
    public function register()
    {
        if(Validate::token('__token__','',['__token__'=>input('param.__token__')]))
        {
            return 111;
        }
        else{
            return 222;
        }
        $result=input('post.');
        var_dump($result);
    }
}