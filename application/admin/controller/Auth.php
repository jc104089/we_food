<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Session;
use think\Request;
use app\admin\model\Admin;
use app\admin\model\Admin_login;
use app\admin\model\Role;
use app\admin\model\Image;
use app\admin\model\User;
use app\admin\model\UserInfo;
use app\admin\model\Board;
use app\admin\model\Material;
use app\admin\model\Book;
use app\admin\model\BookInfo;
use app\admin\model\Message;
use app\admin\model\Comment;
use app\admin\model\Log;
use app\admin\model\LogInfo;
use app\admin\model\Link;
use app\admin\model\Webset;

class Auth extends Controller
{
	protected $admin;
	protected $adminlogin;
	protected $image;
	protected $role;
	protected $is_login = [''];
	protected $user;
	protected $userInfo;
	protected $board;
	protected $material;
	protected $book;
	protected $bookInfo;
	protected $message;
	protected $comment;
	protected $log;
	protected $logInfo;
	protected $link;
	protected $webset;

	public function _initialize()
	{
		$this->admin = new Admin();
		$this->adminlogin = new Admin_login();
		$this->image = new Image();
		$this->role = new Role();
		$this->user = new User();
		$this->userInfo = new UserInfo();
		$this->board = new Board();
		$this->material = new Material();
		$this->book = new Book();
		$this->bookInfo = new BookInfo();
		$this->message = new Message();
		$this->comment = new Comment();
		$this->log = new Log();
		$this->logInfo = new LogInfo();
		$this->link = new Link();
		$this->webset = new Webset();
		if(!$this->checklogin() && in_array('*',$this->is_login)){
			$this->error('没有登录请登录',url('admin/auth/login'));
		}else{			
			$id = session('id');
			//dump($id);die;
			$name = $this->admin->where('aid',$id)->value('username');
			$phone = $this->admin->where('aid',$id)->value('phone');
			$photo = $this->admin->where('aid',$id)->value('photo');
			//dump($photo);die;
			$list = $this->adminlogin->where('aid',$id)->order('id','desc')->paginate(4,true);
			$page = $list->render();
			$data['name'] = $name;
			$data['phone'] = $phone;
			$this->assign('photo',$photo);
			$this->assign('data',$data);
			$this->assign('list',$list);
			$this->assign('page',$page);
		}		
	}
		//登录页面
	public function login()
	{
		//dump($this->request->param());
		return $this->fetch();
	}
	public function checklogin()
	{	
		return session('?id');
	}
	//登录数据处理
	public function dologin()
	{
		//dump($this->request->param());
		$username = $this->request->param('username');
		$userpwd = $this->request->param('userpwd');
		$userpwd = md5($userpwd);
		$res = $this->admin->where(['username'=>$username,
									 'password'=>$userpwd,
									 'islock' =>0,
									])->find();
		//dump($res);die;
		if(!empty($res)){
			$aid = $res->aid;
			$name = $res->username;
			//登录记录
			//$admin = new Admin_login;
			$this->adminlogin->data([
				'aid' => $aid ,
				'name' => $name,
			])->save();
			session('id',$aid);
			session('username',$name);
			$this->success('登录成功',url('admin/index/index'));
		}else{
			$this->error('登陆失败');
		}
	}
}
