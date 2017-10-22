<?php
namespace app\index\controller;

use think\Controller;
use app\index\model\User as UserModel;
use lib\Phoneyz;

class User extends Controller
{
	protected $user;
	public function _initialize()
	{
		$this->user = new UserModel();
	}
    public function login()
    {
    	$data = $this->request->param();
    	//dump($data);
		$result = $this->user->where('username|phone',$data['username'])->where('password',$data['password'])->find();
		echo json_encode($result);
    }
    public function test()
    {

		$this->user->data([
		'username' => 'thinkphp2',
		'password' => 'thinkphp@qq.com',
		'phone'	   => '13333333333',
		]);
		$this->user->allowField(true)->save();
		$user = $this->user->uid;
		dump($user);
		$result = $this->user->where('uid',1)->find();
		dump($result->username);
		/*$newUser = $this->user->find($user);
		// 如果还没有关联数据 则进行新增

		$newUser->userInfo()->save(['phone' => '13333333333']);*/
    }
    public function reg()
    {
    	$filed = ['username','password','phone','repwd','captcha'];
    	$arr = $this->request->param();
    	$key = key($arr);
    	//dump($arr[$key]);
    	if (is_array($key)){
    		$key = 'repwd';
    	}
    	$data = '';
    	if (in_array($key, $filed)){
	    	$result = $this->validate($arr,"User.$key");
			if(true !== $result){
			// 验证失败 输出错误信息
				$data = $result;
			} else {
				$data = '';
			}
		}
		if($key == 'username' && !empty($arr[$key])) { // 查看用户名是否重复
				$result = $this->user->where('username',$arr[$key])->find();
				if ($result) {
					$data = '该用户名已存在';
				}
		}
		if($key == 'phone' && !empty($arr[$key])) { // 查看用户名是否重复
				$result = $this->user->where('phone',$arr[$key])->find();
				if ($result) {
					$data = '该手机号已注册';
				}
		}
		echo json_encode($data);

    }
    //手机验证码
    public function phoneVer()
	{
		$phoneNum = $this->request->param('phone'); 
		//var_dump($_POST);
		if (empty($phoneNum)) {
			echo json_encode('手机号不能空');
		} else {
			$phoneyz = new Phoneyz($phoneNum);
			$phoneyz->getYzm();
			$code = $phoneyz->randNum;
			session('pcode',$code);
			echo $code;
		}
	}
	//注册用户
	public function addUser()
	{
		$data = $this->request->param();
		//dump($data);
		$result = $this->user->allowField(true)->save($data);
		if ($result) {
			echo json_encode('注册成功');
		} else {
			echo json_encode('注册失败');
		}
	}
	// 手机登录
	public function phoneLogin()
	{
		$data = $this->request->param('phone');
		$result = $this->user->where('phone',$data)->find();
		echo json_encode($result);
	}
}
