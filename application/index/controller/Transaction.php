<?php
namespace app\index\controller;

use app\common\Base;
use app\index\model\EcommerceApi;
use app\index\model\DocUserInfo;
use think\Exception;


class Transaction extends Base
{
    public function index()
    {
        $referralCode =session('userid');
        $referralCode =1;
        $DocUserInfo = new DocUserInfo();
        $EcommerceApi = new EcommerceApi();
        //根据推荐人id取得下级所有医生
        $docinfo =$DocUserInfo->alias('D')->where('referralCode',$referralCode)->
        field('user_id,firstName,lastName,contactPhone')
            ->select()->toArray();
        //根据医生，取得所有交易
        foreach ($docinfo as $k =>$v){
            $v['data'] =$EcommerceApi->where('userid',$v['user_id'])->field('userid,pid,unitprice,quantity,created_at')->select()->toArray();
            if($v['data']){
//                $v['data']['Amount'] = $v['data']['unitprice'] * $v['data']['quantity'];
                $data[] =$v;
            }
        }


        $this->assign('data',$data);
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
