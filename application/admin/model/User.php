<?php
namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class User extends Model
{
	use SoftDelete;
	protected $deleteTime = 'delete_time';
	//用户和其子表的一对一关联
	public function userInfo()
	{
		return $this->hasOne('UserInfo','u_id');
	}
	
}