<?php
use think\Route;
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],
//    '[login]'=>[
//
//    ]
    // Route::rule('test','index/login/index','GET|POST'),
     // Route::get('/:name/index','index/:name/index',[],['name'=>'/^\w{6}$/']),
     Route::get('/:name/:action','index/:name/:action',[],['name'=>'/^\w{6}$/']),
    Route::get('/:name','index/index/locationUrl',[],['name'=>'/^\w{6}$/']),

];
