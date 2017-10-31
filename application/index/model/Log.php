<?php
namespace app\index\model;
use think\Model;
use traits\model\SoftDelete;


class Log extends Model
{
	use SoftDelete;
	protected $deleteTime = 'delete_time';
	public function logInfo()
	{
		return $this->hasOne('LogInfo' , 'l_id');
	}
}