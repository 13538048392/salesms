<?php

namespace app\admin\model;

use think\Model;

class Admin extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'sales_admin';
    // 主键
    protected $pk = 'id';

    public function getAdmin($where='1=1'){
    	//获取管理员
    	$admin = Admin::alias('a')
    					 ->join('sales_admin_role s','a.id=s.admin_id','left')
    					 ->join('sales_role r','s.role_id=r.id','left')
    					 ->field('a.id,a.username,a.status,s.*,r.role_name')
    					 ->where($where)
    					 ->select()
    					 ->toArray();
    	// echo Admin::getlastsql();exit;
        $admins = array();
        //获取重复id
        foreach ($admin as $k => $v) {
            if (!array_key_exists($v['id'],$admins)) {
                //获取id存起来
               $admins[$v['id']] = $v;
            }
            else{
                //当出现重复id值的时候叠加角色
               $admins[$v['id']]['role_name'] = $admins[$v['id']]['role_name'].','.$v['role_name'];
            }
            
        }
        // dump($admins);
    	return $admins;
    }


}
