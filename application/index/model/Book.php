<?php

namespace app\index\model;

use think\Model;
use traits\model\SoftDelete;
use app\index\model\User;

class Book extends Model
{
	public function bookInfo()
	{
		return $this->hasOne('BookInfo' , 'c_id');
	}
}