<?php
namespace app\index\model;

use think\Model;

class Profile extends Model
{
	public function user()
	{
		return $this->belongsTo('Data','u_id');
	}
	
}