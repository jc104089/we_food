<?php
namespace app\admin\controller;

use think\Controller;


class Other extends Auth
{
	protected $is_login = ['*'];
	//轮播图
	public function manage_img()
	{
		$count = $this->image->count('pid');
		$list = $this->image->paginate(2);
		 //dump($list);die;
		// // //dump(getLastSql());
		$page = $list->render();
		// dump($page);die;
		$this->assign('image_count',$count);
		$this->assign('image_list',$list);
		$this->assign('image_page',$page);
		return $this->fetch();
	}
	//添加轮播图
	public function add_img()
	{
		$data = $this->request->param();
		// 获取表单上传文件 例如上传了001.jpg
		$file = request()->file('photo');
		// 移动到框架应用根目录/public/uploads/ 目录下
		$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
		if($info){
			// 成功上传后 获取上传信息
			// 输出 jpg
			//$id = session('id');
			//dump($id);
			$info =  $info->getSaveName();
			//dump($info);die;
			// 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
			$infopath = '\\uploads\\' . $info;
			//dump($infopath);
			$data['upurl'] =$infopath;
			//dump($data);
			//$image = new Image();
			$res = $this->image->allowField(true)->save($data);
			if($res){
				$this->success('上传成功',url('admin/other/manage_img'));
			}else{
				$this->error();
			}
		}else{
			// 上传失败获取错误信息
			echo $file->getError();
		}
	}
	//删除轮播图
	public function delete_img()
	{
		$data = $this->request->param();
		$pid = $data['pid'];
		$this->image->destroy($pid);
		$this->redirect('other/manage_img');
	}
}