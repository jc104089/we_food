<?php
namespace app\admin\model;

use think\Model;
use think\SofoDelete;

class Admin_login extends Model
{
	protected $insert = ['ip'];
	protected function setIpAttr()
	{
		return request()->ip();
	}
}