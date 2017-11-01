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
		}
		if(!empty(session('id'))){
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

			//$id = session('id');
			$info = $this->admin->get($id);
			$adminrid = $info->role;
			//$adminrid = $info->toArray();

			//dump($adminrid);die;
			$this->assign('adminrid',$adminrid);
		}

	}
	public function checklogin()
	{	
		return session('?id');
	}
		//登录页面
	public function login()
	{
		//dump($this->request->param());
		return $this->fetch();
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
		//return $this->fetch();


		/*$request = Request::instance();
		 $model = $request->module();
		 $controller = $request->controller();
		 $action = $request->action();

		 //echo $privarr_urls[0];
		// dump($privarr_urls);die;

		
		 $jiedian='/'.$model.'/'.strtolower($controller).'/'.strtolower($action);
		//echo '当前操作'.$jiedian;
       dump($jiedian);die;
       // 	一些页面不需要判断
	 	if(in_array($jiedian, $this->ig_url))
	 	{
	 		//echo 11;
	 		return true;//返回的是什么数据怎么写
	 	}
	// 	//如果是超级管理员已不需要判断
		if(session('user.udertype')==2)
		{
		//	var_dump(session('user'));
			//echo 33;
		//die;	//dump(session('user.is_admin'));die;
			return true;//返回的是什么数据怎么写
		}


	// 	//判断当前访问页面权限的链接是否在用户所有链接中
		if(!in_array($jiedian, $privarr_urls)){
			//echo 33;
			//var_dump($privarr_urls);
			//var_dump($jiedian);die;
			$this->error('你没有权限操作，请联系管理员');
			//echo "你没有权限操作，请联系管理员";
		}*/
	
	
	/*//取出用户的所有权限
	public function getRole($uid=0)	//$uid=0默认
	{
		// if(!$uid &&session('user.id'))		//$uid &&
		// {
			$uid=session('user.uid');
		// }
			
		$privarr_urls=[];
		//取出用户所述的角色
		$role_idss=Db::name('user_role')->where('user_id',$uid)->select();

		if($role_idss){

				foreach ($role_idss as $keys => $values) {
					$role_ids=$values['role_id'];
				}
				//$role_ids = $role_idss['role_id'];
				//dump($role_ids);die;

				if($role_ids)
				{
					//在通过角色取出所述的权限
					$access_ids = [];
					$access_idss=Db::name('role_permession')->where('role_id',$role_ids)->select();
						foreach ($access_idss as $ke => $val) {
							$access_ids[]=$val['node_id'];
						}
						//dump($access_ids);die;
					//在全县表中所有的权限连接
					$urllist=Db::name('permession')->where('id', 'in' ,$access_ids)->select();
					
					if($urllist)
					{
							foreach ($urllist as $key => $value) {
								
								$tmp_urls = $value['name'];
								//dump($tmp_urls);die;
								$privarr_urls[]=$tmp_urls;

							}
							//由于json存入，就json解码
							//$tmp_urls=json_decode($urllist['urls'],true);
							
						
					}
				}

		}
		//dump($role_idss);die;

		
		return $privarr_urls; 
	}*/
		
}
