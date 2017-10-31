<?php

namespace app\index\model;

use think\Model;
use traits\model\SoftDelete;
use app\index\model\User;

class Book extends Model
{
	use SoftDelete;
	protected $deleteTime = 'delete_time';
	public function bookInfo()
	{
		return $this->hasOne('BookInfo' , 'c_id');
	}
}