<?php
namespace app\admin\controller;

use think\Controller;


class Column extends Auth
{
	protected $is_login = ['*'];

	public function category()
	{
		$all_classify = [];
    	$classify = $this->board->where('parent_id',4)->select();
    	//dump($classify);
    	foreach ($classify as $key => $value) {
    		$value = $value->toArray();
    		$value['child_classify'] = $this->board->where('parent_id',$value['id'])->select();
    		$all_classify[$key] = $value;
    	}
    	//dump($all_classify);die;
    	$this->assign('all_classify',$all_classify);
		return $this->fetch();
	}
	//添加栏目
	public function addColumn()
	{
		$data = $this->request->param();
		$data['parent_id'] = $data['fid'];
		//dump($data);die;
		$this->board->allowField(true)->save($data);
		$this->redirect('column/category');
	}
	//修改栏目页面遍历
	public function update_category()
	{
		$data = $this->request->param();
		//dump($data);die;
		$res = $this->board->find($data['id']);
		$res= $res->toArray();
		//dump($res);die;
		$all_classify = [];
    	$classify = $this->board->where('parent_id',4)->select();
    	//dump($classify);
    	foreach ($classify as $key => $value) {
    		$value = $value->toArray();
    		$value['child_classify'] = $this->board->where('parent_id',$value['id'])->select();
    		$all_classify[$key] = $value;
    	}
    	//dump($all_classify);die;
    	$this->assign('all_classify',$all_classify);
		$this->assign('res',$res);
		return $this->fetch();
	}
	//修改栏目
	public function updateColumn()
	{
		$data = $this->request->param();
		//$id = $this->board->where('name',$data['name'])->value('id');
		$data['parent_id'] = $data['fid'];
		//dump($data);die;
		$res = $this->board->allowField(true)->save($data,['id'=>$data['id']]);
		if($res){
			$this->redirect('column/category');
		}else{
			echo "<script>alert('修改失败')</script>";
		}
		
	}
	//删除栏目
	public function deleteColumn()
	{
		$data = $this->request->param();
		//dump($data);die;
		$res = $this->board->destroy($data['id']);
		if($res){
			$this->redirect('column/category');
		}else{
			echo "<script>alert('修改失败')</script>";
		}
	}
}