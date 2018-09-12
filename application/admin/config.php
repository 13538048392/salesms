<?php
/**
 * Created by PhpStorm.
 * User: MAIBENBEN
 * Date: 2018/7/22
 * Time: 17:29
 */
// +----------------------------------------------------------------------
// | 模块设置
// +----------------------------------------------------------------------

// 默认模块名
return[
 'default_module'         => 'admin',
    // 禁止访问模块
    'deny_module_list'       => ['common'],
    // 默认控制器名
    'default_controller'     => 'Index',
    // 默认操作名
    'default_action'         => 'index',
    // 默认验证器
    'default_validate'       => '',
    // 默认的空控制器名
    'empty_controller'       => 'Error',
    // 操作方法后缀
    'action_suffix'          => '',
    // 自动搜索控制器
    'controller_auto_search' => false,
];