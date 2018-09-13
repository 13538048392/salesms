<?php
namespace app\index\controller;

use app\common\Base;
use app\index\model\EcommerceApi;
use think\Exception;


class Transaction extends Base
{
    public function index()
    {
//        $api = new Api();
//        $result =$api->getUsageHistory();
//        dump($result);
        return view('/tranhistory');
    }

    public function getEcommerceApi(){
        $api = new Api();
        $EcommerceApi = new EcommerceApi();
        $startDate =date('Y-m-d',strtotime('-1 day'));
        $endDate = date('Y-m-d');
        $json = $api->getECommerce('',$startDate,$endDate);
        $result =json_decode($json,true);
        if ($result['status'] === 0){
            try{
                $EcommerceApi->add($result['message']);
            }catch (Exception $e){
               $content = $e->getLine().'---'.$e->getMessage();
               $this->log('ecommerceApi',$content);
            }
        }else{
            $content =$json.'---'.$startDate.'-->'.$endDate;
            $this->log('ecommerceApi',$content);
        }

    }
    public function log($type,$content){
        $filename =$_SERVER['DOCUMENT_ROOT']. '/../application/errorlog/'.$type.'.txt';
        $Ts=fopen($filename,"a+");
        fputs($Ts,"执行日期："."\r\n".date('Y-m-d H:i:s',time()).  ' ' . "\n" .$content."\n");
        fclose($Ts);
    }

}
