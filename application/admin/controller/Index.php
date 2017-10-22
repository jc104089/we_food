<?php
namespace app\admin\controller;

use think\Controller;
use app\admin\model\Admin;

class Index extends Controller
{

	public function index()
	{
		$admin = new Admin();
		$count = $admin->count('aid');
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
