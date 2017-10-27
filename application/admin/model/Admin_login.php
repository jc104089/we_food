<?php
namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class Admin_login extends Model
{
	use SoftDelete;
	protected $deleteTime = 'delete_time';
	//自动写入客户端IP
	protected $insert = ['ip'];
	protected function setIpAttr()
	{
		return request()->ip();
	}
}