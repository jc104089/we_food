<?php
namespace app\admin\controller;

use think\Controller;
use app\admin\model\Admin;
use app\admin\model\Admin_login;

class Loginlog extends Auth
{
	protected $is_login = ['*'];
	//单条删除登录记录
	public function one_delete()
	{
		$data = $this->request->param();
		//dump($data);
		$id = $data['id'];
		$this->adminlogin->destroy($id);
		$this->redirect('index/Loginlog');
	}
	//删除自己的登录记录
	public function self_delete()
	{
		$data = $this->request->param();
		//dump($data);
		$aid = $data['aid'];
		$this->adminlogin->destroy(['aid'=>$aid]);
		$this->redirect('index/Loginlog');
	}
	//删除所有的登录记录
	public function all_delete()
	{
		$id = $this->adminlogin->column('id');
		//dump($id);die;
		$this->adminlogin->destroy($id);
		$this->redirect('index/Loginlog');
	}
}