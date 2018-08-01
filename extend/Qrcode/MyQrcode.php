<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/1
 * Time: 14:30
 */
namespace Qrcode;
class MyQrcode{
    public static function showQrcode($content,$file){
       \Qrcode\PHPQRCode\QRcode::png($content,"./tmp/".$file,'L',4,2,true);
       echo '<img src="/tmp/'.$file.'">';
    }
}