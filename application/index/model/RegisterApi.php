<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/12
 * Time: 17:54
 */
namespace app\index\model;


use think\Model;

class RegisterApi extends Model{
    protected $table = 'sales_register_api';
    //获取交易报告数据
    public function getTranReport($referalCode,$startDate=0,$endDate=0){
        if(!$startDate){
            $where =['R.referralCode'=>$referalCode];
        }else{
            $where = ['R.referralCode'=>$referalCode];
            $where['created_at']=array('between',$startDate.','.$endDate);
//            $where['created_at']=array('lt',$endDate);
//            halt($where);
        }

        $data =$this->alias('R')->field('EA.userid,pid,unitprice,quantity,created_at,contactPhone as phone')
            ->join('ecommerce_api EA','R.userId=EA.userid')->order('created_at','desc')
            ->join('doc_user_info DUI','DUI.user_id =EA.userid')->where($where)->select()->toArray();
        return $data;
    }
    
}