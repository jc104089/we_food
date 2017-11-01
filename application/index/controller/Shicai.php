<?php
namespace app\index\controller;

use think\Controller;
use app\index\model\Material;

class Shicai extends Controller
{
	protected $material;
    public function _initialize()
    {
        $this->material = new Material();
    }
    //食材首页
	public function base()
	{
		$board_id = $this->request->param('board_id');
		$board_id = $board_id ? $board_id : 0;
		if($board_id){
			$where = ['board_id'=>$board_id];
		}else{
			$where = '';
		}
		$data = $this->material->where($where)->order('create_time desc')->select();
		//dump($data);
		$this->assign('board_id',$board_id);
		$this->assign('meta_data',$data);
		return $this->fetch();
	}
	// 食材详情页
	public function intro()
	{
		$board_id = $this->request->param('board_id');
		$fid = $this->request->param('fid');
		if(empty($board_id) || empty($fid)){
			 abort(404,'页面不存在');
		}else{
			// 查详情
			$data = $this->material->where('fid',$fid)->find();
			$this->assign('shi_data',$data);
			$this->assign('board_id',$board_id);
			return $this->fetch();
		}
	}
}