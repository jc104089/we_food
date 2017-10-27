<?php
namespace app\index\model;
use think\Model;
use traits\model\SoftDelete;


class Log extends Model
{
	public function logInfo()
	{
		return $this->hasOne('LogInfo' , 'l_id');
	}
}