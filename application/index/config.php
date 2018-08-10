<?php
/**
 * Created by PhpStorm.
 * User: MAIBENBEN
 * Date: 2018/7/22
 * Time: 17:29
 */
return [
    'mail' => [
        'host' => 'smtp.qq.com',
        'smtpauth' => TRUE,
        'username' => '804310470@qq.com',
        'from' => '804310470@qq.com',
        'fromname' => 'salesms.com',
        'password' => 'zgmcoflylpoobfca',//邮箱授权码
        'charset' => 'utf-8',
        'ishtml' => TRUE,
        'smtpdebug'=>0
    ],
    'mail2' => [
        'host' => 'smtp.163.com',
        'smtpauth' => TRUE,
        'username' => '13538048392@163.com',
        'from' => '13538048392@163.com',
        'fromname' => 'salesms.com',
        'password' => 'liuhui583384123',//邮箱授权码
        'charset' => 'utf-8',
        'ishtml' => TRUE,
        'smtpdebug'=>0
    ],
    'sendMessage'=>[
        'accessKeyId'=>'LTAI3NfY6JVzdFI7',
        'accessKeySecret'=>'uyAiiBCMMBAyDmkjo59TZOXhrhHNHP',
        'signName'=>'魔方科技',
        'templateCode'=>'SMS_137865176',
    ]
];
