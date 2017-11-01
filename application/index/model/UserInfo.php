<?php
namespace app\index\model;

use think\Model;
use traits\model\SoftDelete;

class UserInfo extends Model
{
	use SoftDelete;
	protected $deleteTime = 'delete_time';
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