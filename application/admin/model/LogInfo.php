<?php
namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;
class LogInfo extends Model
{
	use SoftDelete;
	protected $deleteTime = 'delete_time';
	public function log()
	{
		return $this->belongsTo('Log','l_id');
	}

}