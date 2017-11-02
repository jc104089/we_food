<?php
namespace app\index\model;

use think\Model;
use traits\model\SoftDelete;

class LogInfo extends Model
{
	//protected $insert = ['ip'];
	use SoftDelete;
	protected $deleteTime = 'delete_time';
	public function log()
	{
		return $this->belongsTo('Log', 'l_id');
	}
	/*protected function setIpAttr()
	{
		return request()->ip();
	}*/
}