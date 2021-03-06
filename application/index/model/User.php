<?php
namespace app\index\model;
use think\Model;
use traits\model\SoftDelete;

class User extends Model
{
	use SoftDelete;
	protected $deleteTime = 'delete_time';
	public function userInfo()
	{
		return $this->hasOne('UserInfo' , 'u_id');
	}
	public function allData($where)
	{
		$user = $this->where($where)->find();
		$result = $user->userInfo->toArray();
	}
}