<?php
namespace app\admin\controller;

use think\Controller;
use app\admin\model\Admin;
use app\admin\model\Admin_login;

class Article extends Auth
{
	protected $is_login = ['*'];
	public function addmaterial()
	{
		$data = $this->board->where('parent_id',3)->select();
		if(!empty($data)){
			$res = [];
			foreach ($data as $key => $value) {
			$res[] = $value->toArray();
			}
			//dump($res);die;
			$this->assign('res',$res);
		}
		return $this->fetch();
	}
	//发布食材
	public function addShicai()
	{
		$data = $this->request->param();
		$img_url = '';
		$img_url = $this->getUpload('img_url');
		//dump($img_url);
		//dump($data);die;
		$res = $this->material->save(['name'=>$data['title'],'content'=>$data['content'],
									  'img_url'=>$img_url,'board_id'=>$data['category'],
									]);
		if($res){
			$this->redirect('article/material');
		}else{
			$this->error('添加失败');
		}
	}
	

	//食材页面便利
	public function material()
	{
		$info = $this->request->param('content');
		//dump($info);
		if(!empty($info)){
			//dump($info);die;
			//$content = $info['content'];
			$where['name'] = ['like',"%"."$info"."%"]; 
			$data = $this->material->where($where)->paginate(2);
			//dump($data);die;
			$page = $data->render();
			$list = [];
				if(!empty($data)){
				$list = $data;				
				}
		}else{
		$list = $this->material->paginate(2);
		$page = $list->render();
		//dump($list);
		//dump($page);die;
		}
		$this->assign('material',$list);
		$this->assign('materialPage',$page);
		return $this->fetch();
	}
	//修改更新食材页面遍历
	public function update_article()
	{
		$result = $this->board->where('parent_id',3)->select();
		if(!empty($result)){
			$res = [];
			foreach ($result as $key => $value) {
			$res[] = $value->toArray();
			}
			//dump($res);die;
			$this->assign('result',$result);
		}
		$data = $this->request->param();
		//dump($data);die;
		$fid = $data['fid'];
		$res = $this->material->where('fid',$fid)->find();
		$res = $res->toArray();
		//dump($res);die;
		$this->assign('info',$res);
		return $this->fetch();
	}
	//修改食材  更新数据库
	public function updateShicai()
	{
		$data = $this->request->param();
		$img_url = $this->getUpload('img_url');
		//dump($img_url);
		//dump($data);die;
		if(!empty($data['title'])){
			$info['name'] = $data['title'];
		}
		if(!empty($data['content'])){
			$info['content'] = $data['content'];
		}
		if(!empty($img_url)){
			$info['img_url'] = $img_url;
		}
		if(!empty($data['category'])){
			$info['board_id'] = $data['category'];
		}
		$res = $this->material->save($info,['fid'=>$data['fid']]);
		if($res){
			$this->redirect('article/material');
		}else{
			$this->error('添加失败');
		}
		
	}
	//删除食材帖子
	public function deleteMaterial()
	{
		$data = $this->request->param();
		//dump($data);
		$fid = $data['fid'];
		$res = $this->material->destroy($fid);
		if($res){
			$this->redirect('article/material');
		}else{
			$this->error('删除失败');
		}
	}
	//所有菜谱遍历
	public function book()
	{
		$count = $this->book->bookInfo()->count('c_id');
		//dump($count);
		$info = $this->request->param('content');
		//dump($info);
		if(!empty($info)){
			//dump($info);die;
			//$content = $info['content'];
			$where['bookname'] = ['like',"%"."$info"."%"]; 
			$data = $this->book->where($where)->paginate();
			//dump($data);die;
			//$list = $this->book->bookInfo()->paginate(2);
			$page = $data->render();
			//dump($data);die;
			$list = [];
				if(!empty($data)){
				$list = $data;
				$list = $this->changeMoreData($list,'bookInfo','c_id');

				$list = $this->selectName($list);				
				}
		}else{
			$list = $this->book->bookInfo()->paginate(2);
			$page = $list->render();
			//dump($list);die;
			$list = $this->changeMoreData($list,'book','c_id');
			$list = $this->selectName($list);
		}
			$this->assign('count',$count);
			$this->assign('BookList',$list);
			$this->assign('BookPage',$page);
			//dump($list);die;
			return $this->fetch();
	}
	//所有的日志的遍历
	public function log()
	{
		$count = $this->log->logInfo()->count('l_id');
		//dump($count);
		$info = $this->request->param('content');
		//dump($info);
		if(!empty($info)){
			//dump($info);die;
			//$content = $info['content'];
			$where['title'] = ['like',"%"."$info"."%"]; 
			$data = $this->log->where($where)->paginate(2);
			//dump($data);die;
			//$list = $this->book->bookInfo()->paginate(2);
			$page = $data->render();
			//dump($data);die;
			$list = [];
				if(!empty($data)){
				$list = $data;
				$list = $this->changeMoreData($list,'logInfo','l_id');

				$list = $this->selectUserName($list);				
				}
		}else{
			$list = $this->log->logInfo()->paginate(2);
			$page = $list->render();
			
			$list = $this->changeMoreData($list,'log','l_id');
			//dump($list);die;
			$list = $this->selectUserName($list);
		}
			//dump($list);die;
			$this->assign('logCount',$count);
			$this->assign('logPage',$page);
			$this->assign('logList',$list);
			return $this->fetch();
	}
	//审核单个日志页面遍历
	public function checkLog()
	{
		$data = $this->request->param();
		$lid = $data['lid'];
		//dump($lid);die;
		$list = $this->log->find($lid);
		$list = $this->changeOneData($list,'LogInfo');
		//dump($list);
		$this->assign('result',$list);
		return $this->fetch();
	}
	//审核单个菜谱页面遍历
	public function checkBook()
	{
		$data = $this->request->param();
		$cid = $data['cid'];
		//dump($cid);die;
		$list = $this->book->find($cid);
		$list = $this->changeOneData($list,'BookInfo');
		//dump($list);die;
		$this->assign('res',$list);
		return $this->fetch();
	}
	//审核日志更改数据库字段
	public function logCheck()
	{
		$data = $this->request->param();
		$lid = $data['lid'];
		//dump($lid);
		$submit = $data['submit'];
		switch ($submit) {
			case 1:
				$this->log->save(['status'=>1],['lid'=>$lid]);
				break;
			case 2:
				$this->log->save(['status'=>2],['lid'=>$lid]);
				break;
			case 3:
				$this->log->save(['status'=>0],['lid'=>$lid]);
				break;
			case 4:
				$this->log->destroy($lid);
				$this->logInfo->destroy($lid);
				break;
		}
		$this->redirect('article/log');
	}
	//审核菜谱更改数据库字段
	public function bookCheck()
	{
		$data = $this->request->param();
		//dump($data);
		$cid = $data['fid'];
		$submit = $data['submit'];
		switch ($submit) {
			case 1:
				$this->book->save(['status'=>1],['cid'=>$cid]);
				break;
			case 2:
				$this->book->save(['status'=>2],['cid'=>$cid]);
				break;
			case 3:
				$this->book->save(['status'=>0],['cid'=>$cid]);
				break;
			case 4:
				$this->book->destroy($cid);
				break;
		}
		$this->redirect('article/book');
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
	public function selectName($data)
	{
		$newdata = [];
		if(empty($data)){
			return false;
		}else{
			foreach ($data as $key => $value) {
				//dump($value);die;
				//$arr = $value->toArray();
				$name = $this->user->where('uid',$value['uid'])->value('username');
				$value['username'] = $name;
				$str = $value['board_id'];
				$str1 = '';
				if(strpos($str, ',')){
					$arr = explode(',', $str);
					foreach ($arr as $k => $v) {
						$boardName = $this->board->where('id',$v)->value('name');
						$str1 .= $boardName . ',';
					}
					$str1 = rtrim($str1,',');
					$value['boardName'] = $str1;
				}else{
					$boardName = $this->board->where('id',$str)->value('name');
					$value['boardName'] = $boardName;
				}
				$newdata[$key] = $value;
			}
			return $newdata;
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
	public function selectUserName($data)
	{
		$newdata = [];
		if(empty($data)){
			return false;
		}else{
			foreach ($data as $key => $value) {
				//dump($value);die;
				//$value = $value->toArray();
				$name = $this->user->where('uid',$value['u_id'])->value('username');
				$value['username'] = $name;
				$newdata[$key] = $value;
			}
			return $newdata;
		}
	}

	/*$str1 = '';
	if(strpos($str, ',')){
		$arr = explode(',', $str);
		foreach ($arr as $key => $value) {
			$name = $this->board->.....
			$str1 .= $name . ',';
		}
		$str1 = rtrim($str1,',');
	}*/
}