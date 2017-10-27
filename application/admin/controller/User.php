<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Session;
use think\Requset;
use app\admin\model\Admin;
use app\admin\model\Admin_login;
class User extends Auth
{
	protected $is_login = ['*'];
	//管理员信息修改（个人修改，还没判断）
	public function update_admin()
	{

		// 获取表单上传文件 例如上传了001.jpg
		$file = request()->file('photo');
		/*$data = $this->request->param();
		dump($data);
		dump($file);die;*/
		if($file){
			// 移动到框架应用根目录/public/uploads/ 目录下
			$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
			if($info){
				// 成功上传后 获取上传信息
				// 输出 jpg
				$id = session('id');
				//dump($id);
				$info =  $info->getSaveName();
				//dump($info);die;
				// 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
				$infopath = '\\uploads\\' . $info;		
				//dump($this->request->param());
				$data = $this->request->param();
				$data['photo'] = $infopath;
				$pwd = $data['password'];
				$password = md5($pwd);
				$data['password'] = $password;
				$res = $this->admin->allowField(true)->save($data,['aid'=>$id]);
				//dump($res);
				//dump($data);die;
				if($res){
					$this->success('修改成功',url('admin/index/index'));
				}else{
					$this->error('修改失败',url('admin/index/index'));
				}
			}else{
			// 上传失败获取错误信息
			echo $file->getError();
			}
		}else{
			$data = $this->request->param();
			$pwd = $data['password'];
			$password = md5($pwd);
			$data['password'] = $password;
			$id = session('id');	
			$res = $this->admin->allowField(true)->save($data,['aid'=>$id]);
			if($res){
				$this->success('修改成功',url('admin/index/index'));
			}else{
				$this->error('修改失败',url('admin/index/index'));
			}
		}		
	}
	//管理员列表（boos专有权限  管理管理员  增删查  改（在个人信息里修改））
	public function manage_admin()
	{
		$count = $this->admin->count('aid');
		 $list = $this->admin->paginate(2);
		 //dump($list);die;
		// // //dump(getLastSql());
		 $page = $list->render();
		// dump($page);die;
		$this->assign('admin_count',$count);
		$this->assign('manage_list',$list);
		$this->assign('manage_page',$page);
		return $this->fetch();
	}
	//角色权限详情
	public function role_power()
	{
		$data = $this->request->param();
		//dump($data['aid']);die;
		//用户和角色多对多查询
		$aid = $data['aid'];
		$info = $this->admin->get($aid);
		$role = [];
		$rid = [];
		foreach ($info->role as $k => $v){
			$role[] = $v->role;
			$rid[] = $v->id;
		}
		//角色和权限多对多查询
		$power = [];
		foreach ($rid as $key => $value){
			$infoo = $this->role->get($value);
				foreach ($infoo->power as $ke => $va){
					$power[] = $va->power;
				}
		}
		//dump($power);
		$power = array_unique($power);
		//dump($power);
		$this->assign('aid',$aid);		
		$this->assign('role',$role);
		$this->assign('power',$power);
		return $this->fetch();
	}
	//增加管理员
	public function add_admin()
	{
		$data = $this->request->param();
		//dump($data);
		$username = $data['username'];
		$password = md5($data['password']);
		$role = $data['role'];
		//dump($role);
		$this->admin->data(['username'=>$username,'password'=>$password])->save();
		$info = $this->admin->where(['username'=>$username,'password'=>$password])->find();
		$aid = $info['aid'];
		//dump($aid);die;
		$infoo = $this->admin->get($aid);
		$res = $infoo->role()->saveAll($role);
		if($res){
				$this->success('添加成功',url('user/manage_admin'));
		}else{
				$this->error('添加失败');
		}
	}
	//删除管理员
	public function delete_admin()
	{
		$data = $this->request->param();
		//dump($data);die;
		$aid = $data['aid'];
		$this->admin->destroy($aid);
		$this->redirect('user/manage_admin');
	}
	//禁用和解锁管理员
	public function lockAdmin()
	{
		$adminid = $this->request->param();
		$aid = $adminid['adminid'];
		//$aid = $data['aid'];
		
		$res = $this->admin->where('aid',$aid)->value('islock');
		//dump($res);
		if($res == 0){
			$this->admin->save(['islock'=>1],['aid'=>$aid]);
			echo 1;
		}else{
			$this->admin->save(['islock'=>0],['aid'=>$aid]);
			echo 0;
		}
	}
	//修改管理员权限（还没加判断）
	public function change_role()
	{
		$data = $this->request->param();
		//dump($data);
		$role = $data['role'];
		//dump($role);
		$aid = $data['aid'];
		$info = $this->admin->get($aid);

		//$adminRole = $info->role;
		$info = $this->changeOneData($info);
		//dump($info);
		$rid = [];
		foreach ($info as $key => $value){
			if(is_array($value)){
				$rid[] = $value['id'];
				}
			}
		//dump($rid);
		// 删除中间表数据
		$infoo = $this->admin->get($aid);
		$infoo->role()->detach($rid);
		$res = $infoo->role()->saveAll($role);
		if($res){
				$this->success('修改成功',url('user/manage_admin'));
		}else{
				$this->error('修改失败');
		}
		
	}
	//退出
	public function doOut()
	{
		Session::clear();
		$this->success('退出成功',url('admin/auth/login'));
	}
	//分页
	public function paging()
	{
		
	}

	public function changeOneData($user)
	{
		if(empty($user)){
			return false;
		} else {
			$user->role;
			$user = $user->toArray();
			//dump($user);
			$childInfo = array_pop($user);
			$all = array_merge($user,$childInfo);
			return $all;
		}
		
	}

}
