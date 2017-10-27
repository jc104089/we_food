<?php
namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class Admin extends Model
{
	use SoftDelete;
	protected $deleteTime = 'delete_time';
	//管理员和角色的多对多关联
	public function role()
	{
		return $this->belongsToMany('Role','admin_role','r_id','a_id');
	}
	public function getIslockAttr($value)
	{
		$islock = [0=>'面壁思过',1=>'回来工作'];
		return $islock[$value];
	}
}