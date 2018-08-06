<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/6
 * Time: 9:46
 */

class QueryingCode
{

   public  function makeQueryingCode($url=''){
        require_once (ROOT_PATH  . 'extend/Qrcode/PHPQRCode/QRcode.php');
        header('Content-Type: image/png');
        $value = $url;					//二维码内容
        $errorCorrectionLevel = 'L';	//容错级别
        $matrixPointSize = 5;			//生成图片大小
        //生成二维码图片
        $QR = \Qrcode\PHPQRCode\QRcode::png($value,false,$errorCorrectionLevel, $matrixPointSize, 2);
        exit;
    }
}