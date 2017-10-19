<?php

namespace app\index\controller;

use app\index\model\Data as DataModel;
use app\index\model\Profile;
use app\index\model\Arc;

use think\Controller;
use think\Loader;


class Data extends Controller
{
	protected $user;
	public function _initialize()
	{
		$this->user = new DataModel; 
	}
	public function add()
	{
		$data = Loader::model('data');
		$info = $data->where('id','3')->find();
		dump($info->nickname);
		//$user = new DataModel;
		/*$user->nickname = 'sanle';
		$user->status = '1';
		$user->save();*/

		/*$user->data(['nickname'=>'淡了','status'=>1]);

		$user->save();*/

		//DataModel::create(['nickname'=>'jhasjd','status'=>1]);
		/*$data = [
			['nickname'=>'sd','status'=>1],
			['nickname'=>'sfd','status'=>0],
			['nickname'=>'fds','status'=>1]
		];
		$user->saveAll($data);*/

		//$user->data(['nickname'=>'结束了','status'=>0]);

		//$user->save();
		//dump($user->id);
	}
	public function updateInfo()
	{
		//$info = DataModel::get(1);
		//return $info->update_time;
		//$info->nickname = '草傻逼';
		//$info->save();
		$info = $this->user->get(5);
		//return $info->status;
		//return $info->getData('status');
		//dump($info->toArray());
		//$info->nickname = 'uuuuu';
		//$info->save();
		//$this->user->save(['nickname'=>'hhhh'],['id'=>10]);
		//$info = $this->user->where('id',10)->find();
		//return $info['nickname'];
		$user = new DataModel(['nickname'=>'HHJJJ','status'=>2]);
		$user->save();
	}
	public function ruan()
	{
		//$this->user::destroy(10);
		dump($this->user->get(10));
		dump($this->user::withTrashed()->where('id',10)->find());
	}
	public function autoInfo()
	{
		//$this->user->nickname = '妈的';
		//$this->user->save();
		$info = $this->user::get(7);
		$info->nickname = 'qqq';
		$info->birthday = '12345';
		$info->save();
	}
/*	public function oneToOne()
	{
		$info = $this->user->get(12);
		echo $info->toJson();
		echo '<br />';
		echo $info->appendRelationAttr('profile',['adds'])->toJson();
		//$info = $this->user->get(12, 'profile');
		//return $info->profile->adds;
		//return $info->adds;
		//$info = Profile::get(1);
		//return $info->user->nickname;
		//$info = $this->user->get(9);
		//return $info->profile()->save(['adds'=>'和购房借款']);
	}
	public function oneToMany()
	{
		//$info = $this->user->get(11);
		//dump($info);
		//foreach ($info->arc as $key => $value) {
			//dump($value->con);
		//}
		/*$info = $this->user::has('arc','>',1)->select();
		dump($info);
		foreach ($info as $key => $value) {
				foreach ($value->arc as $key => $v) {
					dump($v->con);
				}
		}*
		//$info = Arc::get(3);
		//return $info->user->nickname;
	}
	public function ManyToMany()
	{
		//$info = $this->user::get(11);
		//dump($info->role);
		$info = $this->user::get(9);
		//$info->role()->save(['name'=>'销售']);
		$info->role()->save(4);
	}*/

	public function oneToOne()
	{
		//$info = $this->user::get(13);
		//dump($info->profile->adds);

		//$info = $this->user->get(11,'profile');
		//dump($info->adds);

		//$info = Profile::get(2);
		//dump($info->user->nickname);

		$info = $this->user->get(15);
		return $info->profile()->save(['adds'=>'好烦']);
	}

	public function oneToMany()
	{
		/*$info = $this->user::get(13);
		foreach ($info->arc as $key => $value) {
			dump($value->con);
		}*/
		$info = Arc::get(1);
		return $info->user->nickname;
	}

	public function ManyToMany()
	{
		/*$info = $this->user->get(12);
		//dump($info->role);
		foreach ($info->role as $key => $value) {
			dump($value->name);
		}*/
		$info = $this->user->get(14);
		//$info->role()->save(['name'=>'hr']);
		$info->role()->save([1,3,4]);
	}
}