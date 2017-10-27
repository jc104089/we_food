<?php
namespace app\index\model;

use think\Model;
use traits\model\SoftDelete;

class BookInfo extends Model
{
	//protected $insert = ['ip'];
	public function book()
	{
		return $this->belongsTo('Book', 'c_id');
	}
	/*protected function setIpAttr()
	{
		return request()->ip();
	}*/
	public function setContentAttr($value)
	{
		return addslashes($value);
	}
	public function getContentAttr($value)
	{
		return stripslashes($value);
	}
}