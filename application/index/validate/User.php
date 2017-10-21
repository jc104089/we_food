<?php

namespace app\index\validate;

use think\Validate;

class User extends Validate
{
	protected $rule = [
		'username'         => 'require|length:3,25',
		'password'         => 'length:6,16',
		'repwd'              => 'require|confirm:password',
		'phone'            => 'require|regex:^1[34578]\d{9}$',
		'captcha'          =>'require|captcha',
		'email'            => 'email',

	];
	protected $message = [
		'username.require'       => '用户名不能为空',
		'username.length'           => '用户名长度在3-15个字符',
		
		'password.require'   => '密码不能为空',
		'password.length'    => '密码长度在6-16个字符',
		'repwd.require'      => '请确认密码',
		'phone.require'      => '手机号不能空', 
		'phone.regex'        => '手机号码格式不正确',
		'captcha.require'    => '验证码不能为空',
		'captcha.captcha'	 => '验证码不正确',
		'email'              => '邮箱格式错误',
	];
	protected $scene = [
		'username' => ['username'],
		'password' => ['password'=>'require|length:6,16'],
		'repwd'    => ['repwd','password'],
		'phone'    => ['phone'],
		'captcha'  => ['captcha'],
		'email'    => ['email'],
	];
}