<?php
namespace app\index\controller;

use \think\Controller;
use \think\Db;
use app\index\model\Data as UserModel;

class Index extends Controller
{
    protected $user;
    public function _initialize()
    {
      $this->user = new UserModel();
    }
   /* public function index(Request $request)
    {
       // return ['name'=>'nfsa','age'=>13];
    	//$request = Request::instance();
    	//dump($request);
    	//return $request->url(true);
    	//dump($request->param('name'));
    	//dump($request->param('name','WOEOF','strtolower'));
    	//dump($request->only(['name','age']));
    	//dump($request->except('name'));
    	//dump($request->has('name','get'));
    	dump($request->get());
    }
  	public function dataB()
  	{
  		//$result = Db::execute('insert into jd_data(nickname,status) values("jingjing",1)');
  		//dump($result);
  		//$list = Db::table('jd_data')->where('id',5)->find();
  		//$list = Db::name('data')->where('id',5)->select();
  		//dump($list);
  		Db::name('data')->where('status',1)->chunk(2,function($list){
  			dump($list);
  		});
  	}*/

    public function index()
    {
      $info = $this->user->get(1);
      $this->assign('info',$info);
     $this->assign('name','fds');
   //  $this->assign('title','1');
      return $this->fetch();
    }
    
}
