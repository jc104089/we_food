<?php
namespace app\index\model;
use think\Model;
use traits\model\SoftDelete;


class Board extends Model
{
	//查找无限分类
	public function allClass()
	{
		// 分类
    	$all_classify = [];
    	$classify = $this->where('parent_id',4)->select();
    	//dump($classify);
    	foreach ($classify as $key => $value) {
    		$value = $value->toArray();
    		$value['child_classify'] = $this->where('parent_id',$value['id'])->select();
    		$all_classify[$key] = $value;
    	}
    	return $all_classify;
	}
   
}