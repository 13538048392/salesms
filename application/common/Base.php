<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/27
 * Time: 19:14
 */
namespace app\common;
use think\Controller;

class Base extends Controller {
    public function lang() {
        switch ($_GET['lang']) {
            case 'cn':
                cookie('think_var', 'zh-cn');
                break;
            case 'en':
                cookie('think_var', 'en-us');
                break;
            //其它语言
        }
    }
}