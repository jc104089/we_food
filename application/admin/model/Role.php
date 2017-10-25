<?php
namespace app\admin\model;
use think\Model;


class Role extends Model
{
	//角色和权限的多对多关联
	public function power()
	{
		return $this->belongsToMany('Power','role_power','p_id','r_id');
	}
}