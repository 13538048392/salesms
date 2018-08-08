<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/6
 * Time: 9:46
 */
require_once (ROOT_PATH  . 'extend/phpqrcode/phpqrcode.php');
class QueryingCode
{
   public  function makeQueryingCode($url=''){

        $value = $url;					//二维码内容
        $errorCorrectionLevel = 'L';	//容错级别
        $matrixPointSize = 5;			//生成图片大小
        //生成二维码图片
        QRcode::png($value,false,$errorCorrectionLevel, $matrixPointSize, 2);
    }
    public function makeQrCode($url='')
    {
        QRcode::png($url);
    }
}