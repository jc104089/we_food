<?php
namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;
class BookInfo extends Model
{
	use SoftDelete;
	protected $deleteTime = 'delete_time';
	public function book()
	{
		return $this->belongsTo('Book','c_id');
	}

}