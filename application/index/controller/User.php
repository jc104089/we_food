<?php
namespace app\index\controller;

use think\Controller;
use app\index\model\User as UserModel;
use app\index\model\Webset;
use  think\Session;
use lib\Phoneyz;

class User extends Controller
{
	protected $user;
	public function _initialize()
	{
		$this->user = new UserModel();
		
	}
	// 注册
	public function whiteInfo()
	{
		return $this->fetch();
	}
	// 登录
	public function loginInfo()
	{
		return $this->fetch('user/loginnew');
	}
	// 退出
	public function quit()
	{
		if(Session::has('uid')){
			Session::delete('uid');
			session::delete('username');
			$this->success('退出成功',url('index/index/index'));
		}else {
			$this->error('退出失败');
		} 
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
		//dump($user);
		$result = $this->user->where('uid',1)->find();
		dump($result->username);
		/*$newUser = $this->user->find($user);
		// 如果还没有关联数据 则进行新增

		$newUser->userInfo()->save(['phone' => '13333333333']);*/
    }
    // 验证
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
		if ($key == 'submit'){
			$data = '提交按钮';
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
		
		$arr = [
				'username' => $this->request->param('username'),
				'password' => md5($this->request->param('password')),
				'phone'    => $this->request->param('phone'),
		];
		
		$this->user->save($arr);
		$uid = $this->user->uid;
		$newUser = $this->user->find($uid);
		// 如果还没有关联数据 则进行新增
		//获取客户端ip
		$result = $newUser->userInfo()->save(['utype'=>0]);
		if ($this->user->uid && !empty($result)) {
			//echo json_encode('注册成功,请登录');
			$this->success('注册成功,请登录',url('index/index/index'));
		} else {
			//echo json_encode('注册失败');
			$this->error('注册失败');
		}
	}
	// 手机登录
	public function phoneLogin()
	{
		$data = $this->request->param('phone');
		$result = $this->user->where('phone',$data)->find();
		$id = $result->uid;
		$username = $result->username;
		if($id){
			session('uid',$id);
			session('username',$username);
			Webset::where('id',1)->setInc('visitnum',5);
			$this->error('登陆成功',url('index/center/info'));
			//echo json_encode($id);
		} else{
			$this->error('登陆失败');		}
		
	}
	//登录
    public function login()
    {
    	//$data = $this->request->param();
    	//dump($data);
    	$data['password'] = md5($this->request->param('password'));
    	$data['username'] = $this->request->param('username');
		$result = $this->user->where('username|phone',$data['username'])->where('password',$data['password'])->find();
		//dump($result);
		//dump($this->user->getLastSql());
		if ($result){
			$id = $result->uid;
			$username = $result->username;
			session('username',$username);
			session('uid',$id);
			Webset::where('id',1)->setInc('visitnum',5);
			$this->success('登陆成功',url('index/center/info'));
		} else {
			$this->error('登录失败');
		}
    }
	// 找回密码
	public function findPwd()
	{
		$data = $this->request->param('password');
		$phone = $this->request->param('phone');
		$data = md5($data);
		//dump($data);
		$result = $this->user->save(['password'=>$data],['phone'=>$phone]);
		//dump($result);
		if($result){
			$this->success('修改成功',url('index/user/logininfo'));
		}else{
			$this->error('手机号错误或未注册');
		}

	}
	public function changeMoreData($user)
	{
		$data = [];
		if(empty($user)){
			return false;
		} else {
			foreach ($user as $key => $value) {
				$value->userInfo;
				$value = $value->toArray();
				//dump($value);
				$childInfo = array_pop($value);
				$all = array_merge($value,$childInfo);
				$data[$all['uid']] = $all;
			}
			return $data;
		}
		
	}
	public function changeOneData($user)
	{
		if(empty($user)){
			return false;
		} else {
			$user->userInfo;
			$user = $user->toArray();
			//dump($user);
			$childInfo = array_pop($user);
			$all = array_merge($user,$childInfo);
			return $all;
		}
		
	}
}
