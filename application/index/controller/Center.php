<?php
namespace app\index\controller;

use think\Controller;
use app\index\model\User as UserModel;
use  \think\Session;
use  \think\Validate;
use lib\Phoneyz;

class Center extends Controller
{
	protected $user;
	public function _initialize()
	{
		$this->user = new UserModel();
		
	}
	// 个人中心
	public function info()
	{
		/*$part = $this->request->param('part');
		$part = $part ? $part : 0;
		//dump($part);*/
		if(Session::has('uid')){
			$id = Session::get('uid');
			$user = $this->user->find($id);	
			$data = $this->changeOneData($user);
			//dump($data);
			$this->assign('data',$data);
			return $this->fetch();
		}else {
			$this->redirect('index/user/loginInfo');
		}		
	}
	// 修改密码页
	public function changePwd()
	{
		return $this->fetch();
	}
	//修改信息
	public function saveInfo()
	{
		$info = [];
		$data = $this->request->param();
		//dump($data);
		
		foreach ($data as $key => $value) {
			if($key != 'sex'){
				if(!empty($value)){
					$info[$key] = $value;
				}
			} else {
				$info[$key] = $value;
			}
			
		}
		//dump($info);
		$tip = 0;
		if(Session::has('uid')){
			if(empty($info)){
				$tip = 0;
			}else{
				$user = $this->user->where('uid',Session::get('uid'))->find();
				//dump($user);
				$user->userInfo->save($info);
				$tip = 1;
			}		
		}else {
			Session::delete('uid');
			$this->redirect('index/user/loginInfo');
			return false;
		}
		if($tip){
			echo json_encode('保存成功');
		}else{
			echo json_encode('保存失败');
		}

	}
	//修改头像
	public function changePic()
	{
		// 获取表单上传文件 例如上传了001.jpg
		$file = request()->file('photo');
		// 移动到框架应用根目录/public/uploads/ 目录下
		if($file){
			$photo = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
			if($photo){
				// 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
				$path = $photo->getSaveName();
				$path = '\\Uploads\\' . $path;
				$info['photo'] = $path;
				
			}else{
			// 上传失败获取错误信息
				//echo $file->getError();
				$this->error('修改失败');
			}
		}
		$tip = 0;
		if(Session::has('uid')){
			if(empty($info)){
				$tip = 0;
			}else{
				$user = $this->user->where('uid',Session::get('uid'))->find();
				//dump($user);
				$user->userInfo->save($info);
				$tip = 1;
			}		
		}else {
			Session::delete('uid');
			$this->redirect('index/user/loginInfo');
			return false;
		}
		if($tip){
			$this->redirect('index/center/info');
		}else{
			$this->error('修改失败');
		}
	}
	//修改密码
	public function xiugai()
	{
		$data = $this->request->param('password');
		$data = md5($data);
		$tip = 0;
		if(Session::has('uid')){
				$user = $this->user->save(['password'=>$data],['uid'=>Session::get('uid')]);
				//dump($user);
				$tip = 1;		
		}else {
			$this->redirect('index/user/loginInfo');
			return false;
		}
		if($tip){
			Session::delete('uid');
			$this->success('修改成功','index/user/loginInfo');
		}else{
			$this->error('修改失败');
		}
	}
	
	public function check()
	{
		$email = $this->request->param('email');
		//dump($email);
		$validate = new Validate(['email' => 'email']);
		$data = ['email' => $email];
		if (!$validate->check($data)) {
			echo json_encode($validate->getError());
		} else {
			echo 0;
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