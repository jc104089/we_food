<?php
namespace app\index\model;

use think\Model;


class UserInfo extends Model
{
	protected $insert = ['ip'];
	public function user()
	{
		return $this->belongsTo('User', 'u_id');
	}
	protected function setIpAttr()
	{
		return request()->ip();
	}
}