<?php
namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class Book extends Model
{
	use SoftDelete;
	protected $deleteTime = 'delete_time';
	//菜谱和其子表的一对一关联
	public function bookInfo()
	{
		return $this->hasOne('BookInfo','c_id');
	}
	public function getStatusAttr($value)
	{
		$status = [0=>'未审核',1=>'通过',2=>'推荐'];
		return $status[$value];
	}

}