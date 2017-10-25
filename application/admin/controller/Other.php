<?php
namespace app\admin\controller;

use think\Controller;
use app\admin\model\Admin;
use app\admin\model\Admin_login;

class Other extends Auth
{
	protected $is_login = ['*'];
	public function manage_img()
	{
		return $this->fetch();
	}
}