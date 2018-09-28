<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/25
 * Time: 19:25
 */

namespace app\index\controller;

use think\Loader;
use app\admin\model\Role;
use app\admin\model\Url;
use think\Controller;
use think\Db;
use think\Request;

Loader::import('QueryingCode', ROOT_PATH . 'application/entend/QueryingCode.php');

use app\common\Base;

class Team extends Base
{
    public function index(){
        //团队
        return view('/team');
    }
}
