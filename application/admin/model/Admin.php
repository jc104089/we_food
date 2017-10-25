<?php
namespace app\admin\model;

use think\Model;
use think\SofoDelete;

class Admin extends Model
{
	//管理员和角色的多对多关联
	public function role()
	{
		return $this->belongsToMany('Role','admin_role','r_id','a_id');
	}
}