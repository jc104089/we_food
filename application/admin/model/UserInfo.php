<?php
namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;
class UserInfo extends Model
{
	use SoftDelete;
	protected $deleteTime = 'delete_time';
	public function user()
	{
		return $this->belongsTo('User','u_id');
	}
	public function getIslockAttr($value)
	{
		$islock = [0=>'干掉',1=>'复活'];
		return $islock[$value];
	}
}
