<?php
namespace app\index\controller;

use think\Controller;
use app\index\model\User as UserModel;
use app\index\model\Log;
use app\index\model\LogInfo;
use app\index\model\Board;
use app\index\model\Book;
use app\index\model\BookInfo;
use  \think\Session;
use  \think\Validate;
use lib\Phoneyz;

class Center extends Controller
{
	protected $user;
	protected $log;
	protected $board;
	protected $book;
	public function _initialize()
	{
		$this->user = new UserModel();
		$this->log  = new Log();
		$this->board = new Board();
		$this->book = new Book();
		if(!Session::has('uid')){
			Session::delete('uid');
			Session::delete('username');
			$this->redirect('index/user/loginInfo');
			return false;
		}
		
	}
	// 个人中心
	public function info()
	{
		if(Session::has('uid')){
			$id = Session::get('uid');
			$user = $this->user->find($id);	
			$data = $this->changeOneData($user,'userInfo');
			//dump($data);
			$this->assign('aeq',4);
			$this->assign('data',$data);
			return $this->fetch();
		}else {
			$this->redirect('index/user/loginInfo');
		}		
	}
	// 修改密码页
	public function changePwd()
	{
		$this->assign('aeq',4);
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
		if(empty($info)){
			$tip = 0;
		}else{
			$user = $this->user->where('uid',Session::get('uid'))->find();
				//dump($user);
			$user->userInfo->save($info);
			$tip = 1;
		}		
		if($tip){
			echo json_encode('保存成功');
		}else{
			echo json_encode('保存失败');
		}
	}
	//获取上传图片
	public function getUpload($img)
	{
		$file = request()->file($img);
		// 移动到框架应用根目录/public/uploads/ 目录下
		//dump($file);
		if($file){
			$photo = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
			if($photo){
				// 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
				$path = $photo->getSaveName();
				$path = '\\Uploads\\' . $path;
				
				return $path;			
			}else{
			// 上传失败获取错误信息
				return $file->getError();
				//$this->error('修改失败');
				//return false;
			}
		} else {
			return false;
		}
	}
	//修改头像
	public function changePic()
	{
		// 获取表单上传文件 例如上传了001.jpg
		$path = $this->getUpload('photo');
		if($path){
			$info['photo'] = $path;	
		}else{
			$this->error('修改失败');
		}
		$tip = 0;
		if(empty($info)){
			$tip = 0;
		}else{
			$user = $this->user->where('uid',Session::get('uid'))->find();
				//dump($user);
			$user->userInfo->save($info);
			$tip = 1;
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
		$user = $this->user->save(['password'=>$data],['uid'=>Session::get('uid')]);
		//dump($user);
		if($user){
			$tip = 1;
		}				
		if($tip){
			Session::delete('uid');
			Session::delete('username');
			$this->success('修改成功','index/user/loginInfo');
		}else{
			$this->error('修改失败');
		}
	}
	//验证邮箱
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
	public function changeOneData($user,$ar)
	{
		if(empty($user)){
			return false;
		} else {
			$user->$ar;
			$user = $user->toArray();
			//dump($user);
			$childInfo = array_pop($user);
			if(is_array($childInfo)){
				$all = array_merge($user,$childInfo);
			}else {
				$all = $user;
			}			
			return $all;
		}
		
	}
	//日志页
	public function addhuati()
	{
		$data = $this->log->select();
		//dump($data);		
		$data = $this->changeMoreData($data,'loginfo','lid');
		//dump($data);
		$this->assign('aeq',2);
		$this->assign('data',$data);
		return $this->fetch();
	}
	// 发表日志
	public function postHuati()
	{
		$content = $this->request->param('content');
		$title   = $this->request->param('title');
		//dump($content);
		//dump($title);
		$img_url = '';
		if(!empty($content)){
			//正则表达式匹配查找图片路径
			$img_url = $this->getUrl($content);
			$this->log->u_id = Session::get('uid');
			$this->log->title = $title;
			$logInfo = new LogInfo;
			$logInfo->content = $content;
			$logInfo->img_url = $img_url;
			$logInfo->type = 0;
			$this->log->logInfo = $logInfo;
			$this->log->together('logInfo')->save();
			$lid = $this->log->lid;
		}else{
			$this->redirect('index/center/addhuati');
		}
		if ($lid) {
			$this->success('发布成功',url('index/center/addhuati'));
		} else {
			$this->error('发布失败');
		}
	}
	// 删除日志
	public function deHuati()
	{
		$lid = $this->request->param('lid');
		if($lid){
			$result = $this->log->together('logInfo')->destroy($lid);
			if($result){
				echo 1;
			}else{
				echo 0;
			}
		}else{
			echo 0;
		}
	}
	//获取编辑器中图片src
	public function getUrl($content)
	{
		$img_url = '';
		$pattern='/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.jpeg|\.png]))[\'|\"].*?[\/]?>/i';
			preg_match_all($pattern,$content,$res);
			$num=count($res[1]);
			for($i=0;$i<$num;$i++){
				$ueditor_img=$res[1][$i];
				//dump($ueditor_img);
				$img_url .= $ueditor_img . ';';
			}
			return rtrim($img_url,';');
	}
	public function changeMoreData($user,$ar,$id)
	{
		$data = [];
		if(empty($user)){
			return false;
		} else {
			foreach ($user as $key => $value) {
				$value->$ar;
				$value = $value->toArray();
				//dump($value);
				$childInfo = array_pop($value);
				if(is_array($childInfo)){
					$all = array_merge($value,$childInfo);
				} else{
					$all = $value;
				}				
				$data[$all[$id]] = $all;
			}
			return $data;
		}		
	}
	//菜谱页
	public function caipu()
	{
		$cai_board = $this->board->where('parent_id',4)->select();
		$data = $this->book->where('uid',Session::get('uid'))->select();
		$data = $this->changeMoreData($data,'bookInfo','cid');
		//dump($data);
		$this->assign('aeq',1);
		$this->assign('cai_board',$cai_board);
		$this->assign('data',$data);
		return $this->fetch();
	}
	// 发表菜谱
	public function postCai()
	{
		$data = $this->request->param();
		$tips = $data['tips'];
		$content = $data['content'];
		//dump($data);
		$path = $this->getUpload('photo');
		//dump($path);
		if($path){
			$data['photo'] = $path;	
			$img_url = $this->getUrl($content);
			$data['uid'] = Session::get('uid');
			$this->book->data($data);
			$bookInfo = new BookInfo;
			$bookInfo->content = $content;
			$bookInfo->img_url = $img_url;
			$bookInfo->tips = $tips;
			$this->book->bookInfo = $bookInfo;
			$this->book->allowField(true)->together('bookInfo')->save();
			$cid = $this->book->cid;
		}else{
			$this->error('发布失败');
		}
		if ($cid) {
			$this->success('发布成功',url('index/center/caipu'));
		} else {
			$this->error('发布失败');
		}
	}
	//删除菜谱
	public function deCaipu()
	{
		$cid = $this->request->param('cid');
		if($cid){
			$result = $this->book->together('bookInfo')->destroy($cid);
			if($result){
				echo 1;
			}else{
				echo 0;
			}
		}else{
			echo 0;
		}
	}
}