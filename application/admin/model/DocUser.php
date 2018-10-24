<?php

namespace app\admin\model;

use think\Model;

class DocUser extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'sales_doc_user_info';
    // 主键
    protected $pk = 'id';

    public function getDoc($where='1=1'){
      //获取医生
      $data = DocUser::alias('a')
                  ->join('sales_user b','a.referralCode = b.id','left')
                  ->join('sales_channel c','c.id=a.channelId','left')
                  ->field('a.create_time,c.channel_name')
                  ->field(['b.user_name'=>'referee_name','a.contactPhone'=>'phone','a.firstName'=>'first_name','a.lastName'=>'last_name','a.user_id'=>'id'])
                  ->where($where)
                  ->select()
                  ->toArray();
      return $data;
    }
    


}
