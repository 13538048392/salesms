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
   public  function makeQueryingCode($url='',$logoPath,$option =''){
       $value = $url;					//二维码内容
       $errorCorrectionLevel = 'H';	//容错级别
       $matrixPointSize = 6.8;			//生成图片大小
       //生成二维码图片
       ob_start();
       QRcode::png($value,false , $errorCorrectionLevel, $matrixPointSize, 2);
       $ob_contents = ob_get_contents(); //读取缓存区数据
       ob_end_clean();
       $QR = imagecreatefromstring($ob_contents);
       $logo = $logoPath;//准备好的logo图片
       if ($logo!==false) {
           $QR = imagecreatefromstring($ob_contents);   		//目标图象连接资源。
           $logo = imagecreatefromstring(file_get_contents($logo));   	//源图象连接资源。
           $QR_width = imagesx($QR);			//二维码图片宽度
           $QR_height = imagesy($QR);			//二维码图片高度
           $logo_width = imagesx($logo);		//logo图片宽度
           $logo_height = imagesy($logo);		//logo图片高度
           $logo_qr_width = $QR_width / 4;   	//组合之后logo的宽度(占二维码的1/5)
           $scale = $logo_width/$logo_qr_width;   	//logo的宽度缩放比(本身宽度/组合后的宽度)
           $logo_qr_height = $logo_height/$scale;  //组合之后logo的高度
           $from_width = ($QR_width - $logo_qr_width) / 2;   //组合之后logo左上角所在坐标点
           //重新组合图片并调整大小
           /*
            *	imagecopyresampled() 将一幅图像(源图象)中的一块正方形区域拷贝到另一个图像中
            */
           imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,$logo_qr_height, $logo_width, $logo_height);

       }
       if ($option == '') {
         //输出图片
         Header("Content-type: image/png");
         imagepng($QR);
         exit;
       }
       else{
        //base64格式
         ob_start();
         imagejpeg($QR, null, 80);
         $data = ob_get_clean();
         //转换成base64
         $qr = base64_encode($data);
         return $qr;
       }
       
     }

    public function makeQrCode($url='')
    {
        QRcode::png($url);
    }
}