<?php
/**
 * Created by PhpStorm.
 * User: MAIBENBEN
 * Date: 2018/7/22
 * Time: 17:29
 */
return [
    'mail' => [
        'host' => 'md-hk-3.webhostbox.net',
        'smtpauth' => TRUE,
        'username' => 'smtp@kooa.ai',
        'from' => 'smtp@kooa.ai',
        'fromname' => 'sales.kooa.ai',
        'password' => 'i&mVKH6FT2pc',//邮箱授权码
        'charset' => 'utf-8',
        'ishtml' => TRUE,
        'port'=>465,
        'smtpsecure'=>'ssl',
        'smtpdebug'=>2,
    ],
    'mail1' => [
        'host' => 'smtp.163.com',
        'smtpauth' => TRUE,
        'username' => '13538048392@163.com',
        'from' => '13538048392@163.com',
        'fromname' => 'salesms.com',
        'password' => 'liuhui583384123',//邮箱授权码
        'charset' => 'utf-8',
        'port'=>587,
        'smtpsecure'=>'tls',
        'ishtml' => TRUE,
        'smtpdebug'=>0
    ],
    'sendMessage'=>[
        'accessKeyId'=>'LTAI3NfY6JVzdFI7',
        'accessKeySecret'=>'uyAiiBCMMBAyDmkjo59TZOXhrhHNHP',
        'signName'=>'魔方科技',
        'templateCode'=>'SMS_137865176',
    ],
    'level' => 1,
    //推广会员层级
    'api_token' => 'GbwS8JFxJfW3uj86S',

    'redis'=>[
        'host'=>'127.0.0.1',
        'port'=>'6379',
        'author'=>'',
        'db_index'=>0,
    ],
    
    
];
