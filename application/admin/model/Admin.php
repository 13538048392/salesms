<?php

namespace app\admin\model;

use think\Model;

class Admin extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'sales_admin';
    // 主键
    protected $pk = 'id';

    public function getAdmin(){
    	//获取管理员
    	$admin = Admin::alias('a')
    					 ->join('sales_admin_role s','a.id=s.admin_id')
    					 ->join('sales_role r','s.role_id=r.id')
    					 ->field('a.id,a.username,a.status,s.*,r.role_name')
    					 ->select()
    					 ->toArray();
    	// echo Admin::getlastsql();exit;
    	return $admin;
    }
    
}
