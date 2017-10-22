<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Admin;

class User extends Controller
{
	//登录页面
	public function login()
	{
		//dump($this->request->param());
		return $this->fetch();
	}
	//登录数据处理
	public function dologin()
	{
		//dump($this->request->param());
		$username = $this->request->param('username');
		$userpwd = $this->request->param('userpwd');
		$userpwd = md5($userpwd);
		$name = Admin::where('username',$username)->find();
		$pwd = Admin::where('password',$userpwd)->find();
		//dump($name);
		if($name && $pwd){
			session('id',$name->aid);
			$this->success('登录成功',url('admin/index/index'));
		}else{
			$this->error('登陆失败',url('admin/user/login'));
		}
	}
	//管理员信息修改（个人修改）
	public function update_admin()
	{
		dump($this->request->param());
		//获取上传文件（图片）时用file;
		dump($this->request->file());

	}
	//管理员列表（boos专有权限  管理管理员  增删改查）
	public function manage_admin()
	{


		//return $this->fetch();
	}
}
