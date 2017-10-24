<?php
namespace app\index\model;
use think\Model;
use traits\model\SoftDelete;

class User extends Model
{
	use SoftDelete;
	public function userInfo()
	{
		return $this->hasOne('UserInfo' , 'u_id');
	}

}