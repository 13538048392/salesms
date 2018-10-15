<?php
namespace app\index\controller;

use app\common\Base;
use app\index\model\EcommerceApi;
use app\index\model\DocUserInfo;
use app\index\model\RegisterApi;
use app\index\model\TranApi;
use think\Exception;


class Transaction extends Base
{
    public function index()
    {
        $referralCode =session('userid');
//        $referralCode =38;
        $RegisterApi = new RegisterApi();
        $EcommerceApi = new EcommerceApi();
        //根据推荐人id取得下级所有医生
        $docinfo =$RegisterApi->alias('R')->where('referralCode',$referralCode)->
        field('userId')
            ->select()->toArray();
        if (!empty($docinfo)){
            //根据医生，取得所有交易
            foreach ($docinfo as $k =>$v){
                $tranData =$EcommerceApi->where('userid',$v['userId'])->field('userid,pid,unitprice,quantity,created_at')->select()->toArray();
                if($tranData){
//                $v['data']['Amount'] = $v['data']['unitprice'] * $v['data']['quantity'];
                    $data[] =$tranData[0];
                }

            }
        }else{
            $data = [];
        }



        $this->assign('data',$data);
        return view('/tranhistory');
    }
    public function  getUsageApi(){
        $api = new Api();
        $startDate =date('Y-m-d',strtotime('-1 day'));
        $endDate = date('Y-m-d');
        $json =$api->getUsageHistory($startDate,$endDate);
        $result =json_decode($json,true);
        if($result['error'] !=0){
            $content = $result;
            $this->log('getUsageApi',$content);
            return false;
        };


        foreach ($result['data'] as $k=>$v){
            if($a =preg_match('/^\d+$/',$v['referralCode'])){
                $saleData[$k]['referral_id'] =$v['referralCode'];
                foreach ($v['registrations'] as $kkk =>$vvv){
                    $saleData[$k]['registrations'][] =$vvv;
                }
                foreach ($v['features'] as $kk =>$vv){
                    $saleData[$k]['docData'][] =$vv;
                }
            }else{

                $docData[$k]['referral_id'] =$v['referralCode'];
                foreach ($v['registrations'] as $kkk =>$vvv){
                    $docData[$k]['registrations'][] =$vvv;
                }
                foreach ($v['features'] as $kk =>$vv){
                    $docData[$k]['docData'][] =$vv;
                }
            }
        }

        $tranApi = new TranApi();
        $registerApi = new RegisterApi();
        if (isset($saleData)){
            foreach ($saleData as $key =>$val){
                foreach ($val['registrations'] as $kk =>$vv){
                    $regarr[]=[
                        'referralCode'=>$val['referral_id'],
                        'userId'=>$vv['userId'],
                        'registereDate'=>$vv['registeredDate'],
                        'status'=>1,
                    ];
                }
                foreach ($val['docData'] as $k=>$v){
                    $tranarr[]=[
                        'referralCode'=>$val['referral_id'],
                        'userId'=>$v['userId'],
                        'identifier'=>$v['identifier'],
                        'purchaseDate'=>$v['purchaseDate'],
                        'startDate'=>$v['startDate'],
                        'status'=>1,
                    ];
                }
            }
        }

        if (isset($docData)){
            foreach ($docData as $key =>$val){
                foreach ($val['registrations'] as $kk =>$vv){
                    $docregarr[]=[
                        'referralCode'=>$val['referral_id'],
                        'userId'=>$vv['userId'],
                        'registereDate'=>$vv['registeredDate'],
                        'status'=>-1,
                    ];
                }
                foreach ($val['docData'] as $k=>$v){
                    $doctranarr[]=[
                        'referralCode'=>$val['referral_id'],
                        'userId'=>$v['userId'],
                        'identifier'=>$v['identifier'],
                        'purchaseDate'=>$v['purchaseDate'],
                        'startDate'=>$v['startDate'],
                        'status'=>-1,
                    ];
                }
            }
        }

        try{
            $tranApi->insertAll($tranarr);
            $registerApi->insertAll($regarr);
            $registerApi->insertAll($docregarr);
            $tranApi->insertAll($doctranarr);
        }catch (\Exception $e){
            $content = $e->getLine().'---'.$e->getMessage();
            $this->log('getUsageApi',$content);
        }

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
            }catch (\Exception $e){
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
