<?php
namespace app\admin\controller;

use think\Controller;
use app\admin\model\Admin;
use app\admin\model\Admin_login;

class Index extends Auth
{
	protected $is_login = ['*'];
	public function index()
	{
		$lcount = $this->log->count('lid');
		$fcount = $this->material->count('fid');
		$ccount = $this->book->count('cid');
		$allCount = $lcount + $fcount + $ccount;
		$tcount = $this->comment->count('tid');
		$linkcount = $this->link->count('id');
		$visitnum = $this->webset->value('visitnum');
		$count = $this->admin->count('aid');
		$id = session('id');
		//dump($id);die;
		$idcount = $this->adminlogin->where('aid',$id)->count('id');
		//dump($idcount);die;
		$maxid = $this->adminlogin->where('aid',$id)->max('id');
		//dump($maxid);die;
		$adminlogin = $this->adminlogin->find($maxid);
		
		$adminlogin = $adminlogin->toArray();
		//dump($adminlogin);die;
		$this->assign('count',$count);
		$this->assign('tcount',$tcount);
		$this->assign('linkcount',$linkcount);
		$this->assign('allcount',$allCount);
		$this->assign('visitnum',$visitnum);
		$this->assign('idcount',$idcount);
		$this->assign('adminlogin',$adminlogin);
		return $this->fetch();
	}
	public function add_category()
	{
		return $this->fetch();
	}
	public function add_flink()
	{
		return $this->fetch();
	}
	//增加友情链接插入数据库
	public function addLink()
	{
		$data = $this->request->param();
		//dump($data);
		$res = $this->link->allowField(true)->save($data);
		if($res){
			$this->redirect('index/flink');
		}else{
			$this->error('发送失败');
		}
	}
	//通知页面遍历
	public function notice()
	{
	
		$list = $this->message->paginate(2);
		$page = $list->render();
		//$data = $list->toArray();
		$data = $this->selectName($list); 
		//dump($data);die;
		$this->assign('massage',$data);
		$this->assign('massagePage',$page);
		
		return $this->fetch();
	}
	//发送通知页面遍历
	public function add_notice()
	{

		return $this->fetch();
	}
	//发送通知插入数据库
	public function postMessage()
	{
		$data = $this->request->param();
		//dump($data);
		$name = $data['name'];
		$data['guid'] = $this->user->where('username',$name)->value('uid');
		//dump($data);
		$res = $this->message->allowField(true)->save($data);
		if($res){
			$this->redirect('index/notice');
		}else{
			$this->error('发送失败');
		}
	}
	//删除通知
	public function deleteMessage()
	{
		$data = $this->request->param();
		$mid = $data['mid'];
		//dump($mid);
		$res = $this->message->destroy($mid);
		if($res){
			$this->redirect('index/notice');
		}else{
			$this->error('删除失败');
		}
	}
	//评论页面的遍历
	public function comment()
	{
		$info = $this->request->param('content');
		//dump($info);die;
		if(!empty($info)){
			//dump($info);die;
			//$content = $info['content'];
			$where['content'] = ['like',"%"."$info"."%"]; 
			$list = $this->comment->where($where)->paginate(4);
			$page = $list->render();
			$data = [];
				if(!empty($list)){
					$data = $this->selectUserName($list);
					//dump($data);die;
				}
			
		}else{
			$list = $this->comment->paginate(4);
			$page = $list->render();
			$data = $this->selectUserName($list);
			//dump($data);die;
		}
		$this->assign('comment',$data);
		$this->assign('commentPage',$page); 
		return $this->fetch();
	}
	//删除评论（单删）
	public function deleteComment()
	{
		$data = $this->request->param();
		$tid = $data['tid'];
		//dump($tid);
		$res = $this->comment->destroy($tid);
		if($res){
			$this->redirect('index/comment');
		}else{
			$this->error('删除失败');
		}
	}
	//多删评论
	public function deleteMore()
	{
		$data = $this->request->param();
		//dump($data);
		$data = $data['checkbox'];
		foreach ($data as $key => $value) {
				$res = $this->comment->destroy($value);
			}
		if($res){
			$this->redirect('index/comment');
		}else{
			$this->error('删除失败');
		}
		
	}
	public function flink()
	{
		$list = $this->link->paginate(2);
		$page = $list->render();
		$this->assign('linkList',$list);
		$this->assign('linkPage',$page);
		return $this->fetch();
	}
	//登录记录页面遍历
	public function loginlog()
	{
		$aid = session('id');
		//$adminlogin = new Admin_login();
		$count = $this->adminlogin->count('id');
		$list = $this->adminlogin->order('id','desc')->paginate(4);
		// 获取分页显示
		$page = $list->render();
		$this->assign('aid',$aid);
		$this->assign('count',$count);
		$this->assign('list', $list);
		$this->assign('page', $page);
		//return $this->fetch('index/loginlog');
		//$this->assign('dd',$dd);
		return $this->fetch();
	}
	//普通用户页面遍历
	public function manage_user()
	{
		$count = $this->user->userInfo()->where('utype',0)->count('u_id');
		//dump($count);
		$list = $this->user->userInfo()->where('utype',0)->paginate(1);
		$page = $list->render();
		//dump($list);die;
		$list = $this->changeMoreData($list,'user','u_id');
		//die;
		//dump($page);
		$this->assign('count',$count);
		$this->assign('list',$list);
		$this->assign('page',$page);
		return $this->fetch();
	}
	//会员页面遍历
	public function manage_vip()
	{
		$count = $this->user->userInfo()->where('utype',1)->count('u_id');
		//dump($count);die;
		$list = $this->user->userInfo()->where('utype',1)->paginate(1);
		$page = $list->render();
		//dump($list);die;
		$list = $this->changeMoreData($list,'user','u_id');
		//dump($list);
		//dump($page);
		$this->assign('count',$count);
		$this->assign('list',$list);
		$this->assign('page',$page);
		return $this->fetch();
	}
	
	public function readset()
	{
		return $this->fetch();
	}
	//网站设置的遍历
	public function setting()
	{
		$data = $this->webset->find();
		$data = $data->toArray();
		//dump($data);die;
		$this->assign('webset',$data);
		return $this->fetch();
	}
	//修改网站设置和关闭站点
	public function setweb()
	{
		$data = $this->request->param();
		//$data = $data->toArray();
		//dump($data);die;
		$res = $this->webset->allowField(true)->save($data,['id'=>1]);
		if($res){
			$this->redirect('index/setting');
		}else{
			$this->error('修改失败');
		}
	}
	//修改友情链接页面遍历（单个）
	public function update_flink()
	{
		$data = $this->request->param();
		//dump($data);die;
		$id = $data['id'];
		$linkinfo = $this->link->find($id);
		$linkinfo = $linkinfo->toArray();
		//dump($linkinfo);die; 
		$this->assign('linkinfo',$linkinfo);
		return $this->fetch();
	}
	//修改友情链接更改数据库
	public function updateLink()
	{
		$data = $this->request->param();
		//dump($data);
		$id = $data['id'];
		$res = $this->link->allowField(true)->save($data,['id'=>$id]);
		if($res){
			$this->redirect('index/flink');
		}else{
			$this->error('修改失败');
		}

	}
	//删除友情链接
	public function deleteLink()
	{
		$data = $this->request->param();
		//dump($data);
		$id = $data['id'];
		$res = $this->link->destroy($id);
		if($res){
			$this->redirect('index/flink');
		}else{
			$this->error('删除失败');
		}
	}
	public function changeMoreData($user,$ar,$id)
	{
		$data = [];
		if(empty($user)){
			return false;
		} else {
			foreach ($user as $key => $value) {
				$value->$ar;
				//dump($value);
				$value = $value->toArray();
				//dump($value);
				$childInfo = array_pop($value);
				if(is_array($childInfo)){
					$all = array_merge($value,$childInfo);
				} else{
					$all = $value;
				}	
				//dump($all);			
				$data[$all[$id]] = $all;
			}
			return $data;
		}		
	}

	public function selectName($data)
	{
		$newdata = [];
		if(empty($data)){
			return false;
		}else{
			foreach ($data as $key => $value) {
				//dump($value);die;
				$value = $value->toArray();
				$name = $this->user->where('uid',$value['guid'])->value('username');
				$value['username'] = $name;
				$newdata[$key] = $value;
			}
			return $newdata;
		}
	}
	public function selectUserName($data)
	{
		$newdata = [];
		if(empty($data)){
			return false;
		}else{
			foreach ($data as $key => $value) {
				//dump($value);die;
				$value = $value->toArray();
				$name = $this->user->where('uid',$value['uid'])->value('username');
				$value['username'] = $name;
				$newdata[$key] = $value;
			}
			return $newdata;
		}
	}

}
