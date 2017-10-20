<?php
namespace app\index\controller;

use think\Controller;
use \think\Validate;

class User extends Controller
{
	protected $rule = [
		'name'       => 'require|length:3,25',
		'password'   => 'require|length:6,16',
		'repassword' =>'require|confirm:password',
		'phone'      => 'require|regex:^1[34578]\d{9}$',
		
		'email'      => 'email',

	];
	protected $message = [
		'name.require'       => '用户名不能为空',
		'name.length'        => '用户名长度在3-25个字符',
		'password.require'   => '密码不能为空',
		'password.length'    => '密码长度在6-16个字符',
		'repassword.confirm' => '两次密码输入不一致',
		'phone.require'      => '手机号不能空', 
		'phone.regex'        => '手机号码格式不正确',
		
		'email'              => '邮箱格式错误',
	];
	protected $scene = [
		'username' => ['name'],
		'password' => ['password'],
		'repwd'    => ['repassword'],
		'phone'    => ['phone'],
		
		'email'    => ['email'],
	];
    public function login()
    {
    	return $this->fetch();
    }
    public function reg()
    {
    	$arr = $this->request->param();
    	$key = key($arr);

		$validate = new Validate($this->rule,$this->message);
		//dump($this->rule);
		$result = $validate($arr);
		//dump(json_encode($key));
		echo $validate->getError();
    	//echo json_encode($arr);
    	//return $this->fetch();
    }
}
