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


        $user_id =array_column($docinfo, 'user_id');
        //根据医生，取得所有交易
        $where['userid']=array('in',$user_id);
        $list =$EcommerceApi->where($where)->field('userid,pid,unitprice,quantity,created_at')->paginate(10);
        $this->assign('list',$list);
        foreach ($docinfo as $k =>$v){
           $docinfo[$user_id[$k]] =$v;
           unset($docinfo[$k]);
        }

        $this->assign('docinfo',$docinfo);
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
