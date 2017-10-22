<?php

namespace app\index\model;

use \think\Model;
use traits\model\SoftDelete;

class User extends Model
{
	public function userInfo()
	{
		return $this->hasOne('UserInfo','u_id');
	}
	public function userSel($where = null)
	{

	}
	public function findOne($where=null)
	{
		return $this->where($where)->find();
	}
}