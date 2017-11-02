<?php
namespace app\index\controller;

use think\Controller;
use app\index\model\User as UserModel;
use app\index\model\Log;
use app\index\model\LogInfo;
use app\index\model\Board;
use app\index\model\Book;
use app\index\model\BookInfo;
use app\index\model\Save;
use app\index\model\Message;
use  \think\Session;
use  \think\Validate;
use lib\Phoneyz;

class Center extends Controller
{
	protected $user;
	protected $log;
	protected $board;
	protected $book;
	protected $saveCai;
	protected $message;
	public function _initialize()
	{
		$this->user = new UserModel();
		$this->log  = new Log();
		$this->board = new Board();
		$this->book = new Book();
		$this->saveCai = new Save();
		$this->message = new Message();
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
		$status = $this->request->param('status');
		if(is_null($status)){
			$status = 1;
		}
		if($status == 1){
			$where['status'] = ['in','1,2']; 
		}else{
			$where['status'] = ['=',0];
		}
		$data = $this->log->where($where)->paginate(1);
		$page = $data->render();
		//dump($data);		
		$data = $this->changeMoreData($data,'loginfo','lid');
		if($data){
			foreach ($data as $key => $value) {
				if($value['img_url']){
					$value['all_img'] = explode(';', $value['img_url']);
				}
				$data[$key] = $value;
			}
		}
		//dump($data);die;
		$this->assign('status',$status);
		$this->assign('page',$page);
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
			$result1 = $this->log->destroy($lid);
			$result = LogInfo::destroy(['l_id'=>$lid]);
			if($result&&$result1){
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
		$status = $this->request->param('status');
		if(is_null($status)){
			$status = 1;
		}
		/*$board_style = $this->board->where('parent_id',12)->select();
		$board_tyle = $this->board->where('parent_id',13)->select();*/	
		$allClass = $this->board->allClass();
		//dump($allClass);die;	
		$data = $this->book->where('uid',Session::get('uid'))->where('status',$status)->paginate(1);
		$page = $data->render();
		$data = $this->changeMoreData($data,'bookInfo','cid');
		//dump($data);die;
		$this->assign('aeq',1);
		/*$this->assign('board_style',$board_style);
		$this->assign('board_tyle',$board_tyle);*/
		$this->assign('status',$status);
		$this->assign('page',$page);
		$this->assign('allClass',$allClass);
		$this->assign('data',$data);
		return $this->fetch();
	}
	// 发表菜谱
	public function postCai()
	{
		$data = $this->request->param();
		$data['board_id'] = join(',',$data['board_id']);
		$tips = $data['tips'];
		$content = $data['content'];
		//dump($data);die;
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
			$result1 = $this->book->destroy($cid);
			$result = BookInfo::destroy(['c_id'=>$cid]);
			if($result&&$result1){
				echo 1;
			}else{
				echo 0;
			}
		}else{
			echo 0;
		}
	}
	//收藏页
	public function mySave()
	{
		$data = $this->saveCai->where('u_id',Session::get('uid'))->paginate(2);
		$page = $data->render();
		$all_data = [];
		foreach ($data as $key => $value) {
			$cai_data = $this->book->where('cid',$value['data_id'])->find();
			$cai_data = $this->changeOneData($cai_data,'bookInfo');
			$cai_data['username'] = $this->user->where('uid',$cai_data['uid'])->value('username');
			$cai_data['save_id'] = $value['id'];		
			$all_data[$key] = $cai_data;
		}
		//dump($all_data);
		$this->assign('page',$page);
		$this->assign('all_data',$all_data);
		$this->assign('aeq',3);
		return $this->fetch();
	}
	//删除收藏
	public function delSave()
	{
		$id = $this->request->param('id');
		if($id){
			$result = $this->saveCai->destroy($id);
			if ($result) {
				echo 1;
			}else{
				echo 0;
			}
		}else{
			echo 0;
		}
	}
	//会员页
	public function vipcenter()
	{
		// 查消息
		//$message_data = $this->message->where('mid',1)->find();
		//dump($message_data);die;
		$uid = Session::get('uid');
		$message_data = $this->message->where('guid', $uid)->paginate(1);
		$message_page = $message_data->render();
		$userdata = $this->user->where('uid',Session::get('uid'))->find();
		$userdata = $this->changeOneData($userdata,'userInfo');
		//dump($message_data);die;
		$this->assign('userdata',$userdata);
		$this->assign('message_data',$message_data);
		$this->assign('message_page',$message_page);
		$this->assign('aeq',0);
		return $this->fetch();
	}
	public function delMes()
	{
		$mid = $this->request->param('mid');
		if($mid){
			$result = $this->message->destroy($mid);
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