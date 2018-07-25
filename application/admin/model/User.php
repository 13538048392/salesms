<?php

namespace app\admin\model;

use think\Model;

class User extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'sales_user';
    // 主键
    protected $pk = 'user_id';

    public function getMemberList(){
        //获取会员信息
        //管理员推荐人的会员信息
        $admin_referee = User::alias('a')
                              ->join('sales_admin b','a.referee_id = b.id')
                              ->join('sales_channel c','c.channel_id=a.channel_id')
                              ->join('sales_userinfo d','d.user_id = a.user_id','left')
                              ->field('a.user_id,a.user_name,a.email,a.status,a.create_time,c.channel_name,d.phone')
                              ->field(['b.username'=>'referee_name'])
                              ->where('admin_id','<>',0)
                              ->select()
                              ->toArray();
        //普通会员推荐人的会员信息
        $user_referee = User::alias('a')
                              ->join('sales_user b','a.referee_id = b.user_id')
                              ->join('sales_channel c','c.channel_id=a.channel_id')
                              ->join('sales_userinfo d','d.user_id = a.user_id','left')
                              ->field('a.user_id,a.user_name,a.email,a.status,a.create_time,c.channel_name,d.phone')
                              ->field(['b.user_name'=>'referee_name'])
                              ->where('c.user_id','<>',0)
                              ->select()
                              ->toArray();
        // dump($admin_referee);
        // dump($user_referee);
        //结果合并返回
        return array_merge($admin_referee,$user_referee);
    }


}
