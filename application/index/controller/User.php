<?php
namespace app\index\controller;

use think\Controller;
use \think\Validate;

class User extends Controller
{
	/*protected $rule = [
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
	];*/
    public function login()
    {
    	return $this->fetch();
    }
    public function reg()
    {
    	$filed = ['username','password','phone','repwd'];
    	$arr = $this->request->param();
    	$key = key($arr);
    	if (is_array($key)){
    		$key = $key['repwd'];
    	}
    	if (in_array($key, $filed)){
	    	$result = $this->validate($arr,"User.$key");
			if(true !== $result){
			// 验证失败 输出错误信息
				echo json_encode($result);
			}
		}else {
			echo $key;
		}

		/*public function phoneVer()
	{
		//var_dump($_POST);
		if (empty($_POST['phoneNum'])) {
			exit("<script>alert('请输入手机号');window.location.href='index.php?m=index&c=index&a=reg'</script>");
		} else {
			$phoneyz = new Phoneyz($_POST['phoneNum']);
			$phoneyz->getYzm();
			$_SESSION['pcode'] = $phoneyz->randNum;
			header('location:index.php?m=index&c=index&a=reg');
		}
	
		
		//var_dump($pcode);
	}*/
    }
}
