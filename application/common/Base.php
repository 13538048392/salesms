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
                cookie('think_var', 'zh-cn',3600);
                break;
            case 'en':
                cookie('think_var', 'en-us',3600);
                break;
            case 'hk':
                cookie('think_var', 'zh-hk',3600);
                break;
            //其它语言
        }
    }
}