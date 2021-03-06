<?php
namespace app\index\model;

use think\Model;
use traits\model\SoftDelete;

class BookInfo extends Model
{
	//protected $insert = ['ip'];
	use SoftDelete;
	protected $deleteTime = 'delete_time';
	public function book()
	{
		return $this->belongsTo('Book', 'c_id');
	}
	/*protected function setIpAttr()
	{
		return request()->ip();
	}*/
	protected function setContentAttr($value)
    {
        $data = strtolower(str_replace('\\', '/', $value));
        return $data;
    }
    protected function setImgUrlAttr($value)
    {
        $data = strtolower(str_replace('\\', '/', $value));
        return $data;
    }
}