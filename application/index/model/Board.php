<?php
namespace app\index\model;
use think\Model;
use traits\model\SoftDelete;


class Board extends Model
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
	//查找菜谱无限分类
	public function allClass()
	{
		// 分类
    	$all_classify = [];
    	$classify = $this->where('parent_id',4)->select();
    	//dump($classify);
        if($classify){
            foreach ($classify as $key => $value) {
                $value = $value->toArray();
                $value['child_classify'] = $this->where('parent_id',$value['id'])->select();
                $all_classify[$key] = $value;
            }
             return $all_classify;
        }else{
            return false;
        }
    	
	}
    //查分类板块
    public function boardClass()
    {
        $board = $this->where('parent_id','in',[12,13])->select();
        $board = array_slice($board, 0, 8);
        if ($board) {
            return $board;
        }else {
            return false;
        }
    }
    
   
}