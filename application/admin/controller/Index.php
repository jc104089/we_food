<?php
namespace app\admin\controller;

use think\Controller;
use app\admin\model\Admin;
use app\admin\model\Admin_login;

class Index extends Auth
{
	protected $is_login = ['*'];
	public function index()
	{
		
		//$admin = new Admin();
		$count = $this->admin->count('aid');
		$this->assign('count',$count);
		return $this->fetch();
	}
	public function add_article()
	{
		
		return $this->fetch();
	}
	public function add_category()
	{
		return $this->fetch();
	}
	public function add_flink()
	{
		return $this->fetch();
	}
	public function add_notice()
	{
		return $this->fetch();
	}
	public function article()
	{
		return $this->fetch();
	}
	public function category()
	{
		return $this->fetch();
	}
	public function comment()
	{
		return $this->fetch();
	}
	public function flink()
	{
		return $this->fetch();
	}
	public function loginlog()
	{
		//$adminlogin = new Admin_login();
		$count = $this->adminlogin->count('id');
		$list = $this->adminlogin->order('id','desc')->paginate(4);
		// 获取分页显示
		$page = $list->render();
		$this->assign('count',$count);
		$this->assign('list', $list);
		$this->assign('page', $page);
		//return $this->fetch('index/loginlog');
		//$this->assign('dd',$dd);
		return $this->fetch();
	}
	public function manage_user()
	{
		return $this->fetch();
	}
	public function notice()
	{
		return $this->fetch();
	}
	public function readset()
	{
		return $this->fetch();
	}
	public function setting()
	{
		return $this->fetch();
	}
	public function update_article()
	{
		return $this->fetch();
	}
	public function update_category()
	{
		return $this->fetch();
	}
	public function update_flink()
	{
		return $this->fetch();
	}

}
