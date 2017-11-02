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
	//查对应版块内容
    public function boardData($status)
    {
        if($status == 0){
            $where['status'] = ['=',3];
        }else {
            $where['status'] = ['neq',0];
            if($status != 1){
                $where['board_id'] = ['like',"%"."$status"."%"]; 
            }
        }
        $data = $this->where($where)->field('cid,uid,bookname,photo,status')->select();
        if($data){
            return $data;
        }else{
            return false;
        }
    }
}