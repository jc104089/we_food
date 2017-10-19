<?php
namespace app\index\model;

use think\Model;
use traits\model\SoftDelete;

class Data extends Model
{
	use SoftDelete;
	// 自动完成
	//protected $auto = ['birthday'=>'1508219308','status'=>0];
	protected $insert = ['status'=>'1'];
/*
	//一对一关联
	public function profile()
	{
		return $this->hasOne('profile','u_id')->bind('adds');
	}
	// 一对多关联
	public function arc()
	{
		return $this->hasMany('arc','u_id');
	}
	// 多对多关联
	public function role()
	{
		return $this->belongsToMany('Role','data_role','r_id','u_id');
	}*/
	public function getStatusAttr($value) {
		$status = [-1=>'删除',0=>'禁用',1=>'正常',2=>'待审核'];
		return $status[$value];
	}
	public function setNicknameAttr($value)
	{
		return strtolower($value);
	}
	// 一对一关联
	public function profile()
	{
		//return $this->hasOne('Profile','u_id');
		return $this->hasOne('Profile','u_id')->bind('adds');
	}

	// 一对多关联
	public function arc()
	{
		return $this->hasMany('Arc','u_id');
	}

	// 多对多关联
	public function role()
	{
		return $this->belongsToMany('Role','data_role','r_id','u_id');
	}

}