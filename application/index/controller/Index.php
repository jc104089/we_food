<?php
namespace app\index\controller;

use think\Controller;
use app\index\model\Book;
use app\index\model\User;
use app\index\model\BookInfo;
use app\index\model\Board;
use app\index\model\Log;
use app\index\model\LogInfo;
use app\index\model\Material;
use app\index\model\Image;
use app\index\model\Link;
use app\index\model\Webset;
use think\Db;

class Index extends Controller
{
  	protected $book;
  	protected $user;
  	protected $board;
  	protected $log;
  	protected $material;
  	protected $image;
	public function _initialize()
	{
		$this->book = new Book();
		$this->user = new User();
		$this->board = new Board();
		$this->log = new Log();
		$this->material = new Material();
		$this->image = new Image();
		$link = Link::select();
		$webdata = Webset::find(1);
		$this->assign('webdata',$webdata);
		$this->assign('link',$link);
	}
    public function index()
    {
    	//查轮播图
    	$move_pic = $this->image->select();
    	//查菜谱分类
    	$cai_class = $this->board->allClass();
    	// 查食材分类
    	$shi_class = $this->board->where('parent_id',3)->select();
    	// 菜谱新秀
    	$new_cai = $this->book->order('create_time desc')->where('status','neq',0)->limit(16)->select();
    	$new_cai = $this->changeMoreData($new_cai,'bookInfo','cid');
    	$new_cai = $this->selectName($new_cai);
    	//dump($new_cai);die;
    	// 最受欢迎菜谱
    	$like_cai = BookInfo::field('c_id')->order('goodnum desc')->select();
    	foreach ($like_cai as $key => $value) {
    		$all_id[] = $value['c_id'];    		
    	}
    	
    	$like_cai = $this->book->where('cid','in',$all_id)->where('status','neq',0)->limit(16)->select();
		$like_cai = $this->selectName($like_cai);
		//dump($like_cai);die;
		// 查食材
		$tui_material = $this->material->limit(7)->select();
		//dump($tui_material);die;
		// 热门日志
		$hot_log = $this->log->hotLog();
		$hot_log = $this->selectName($hot_log,true,'u_id');
		$hot_log = $this->getImg($hot_log);
		//推荐日志
		$tui_log = $this->log->hotLog();
		$tui_log = $this->selectName($tui_log,true,'u_id');
		$tui_log = $this->getImg($tui_log);
		//dump($hot_log);die;
    	$this->assign([
    		'move_pic'     =>$move_pic,
    		'cai_class'    => $cai_class,
    		'shi_class'    => $shi_class,
    		'new_cai'      => $new_cai,
    		'like_cai'     => $like_cai,
    		'tui_material' => $tui_material,
    		'hot_log'      => $hot_log,
    		'tui_log'	   => $tui_log,
    	]);
    	return $this->fetch();
    }
    public function getImg($log)
    {
    	$i = 0;
    	foreach ($log as $key => $value) {
    		if(!empty($value['logInfo']['img_url'])){
    			$value['all_img'] = array_slice(explode(';', $value['logInfo']['img_url']), 0,4) ;
    		}
    		$log[$key] = $value;
    	}
    	return $log;
    }
    public function selectName($data,$pho=false,$uid='uid')
	{
		$newdata = [];
		if(empty($data)){
			return false;
		}else{
			foreach ($data as $key => $value) {
				if(!is_array($value)){
					$arr = $value->toArray();
				}else{
					$arr = $value;
				}
				$name = $this->user->find($value[$uid]);
				$arr['username'] = $name->username;
                if($pho){
                    $name->userInfo;
                    $name = $name->toArray();
               // dump($name);
                $arr['photo'] = $name['userInfo']['photo'];
                }
                
				$newdata[$key] = $arr;
			}
			return $newdata;
		}
	}
   	public function changeMoreData($user,$ar,$id,$type=false)
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
				if($type){
					$all['type'] = 1;
				}	
				$data[$all[$id]] = $all;
			}
			return $data;
		}		
	}
	
	public function search()
	{

		$searchContent = $this->request->param('searchContent');
		dump($searchContent);
		$end_data = [];
		if(!empty($searchContent)){
			$cai_data = $this->book->where('bookname','like','%'.$searchContent.'%')->where('status','neq',0)->select();
			$cai_data = $this->changeMoreData($cai_data,'bookInfo','cid',true);
			$shi_data = $this->material->where('content','like','%'.$searchContent.'%')->select();
			$shi_new  = []; 
			if($shi_data){
				foreach ($shi_data as $key => $value) {
					$value = $value->toArray();
					$value['photo'] = $value['img_url'];
					$value['type']  = 2;
					$shi_new[$key]  = $value;
				}
			}else{
				$shi_new = $shi_data;
			}
			
			if(is_array($cai_data) && is_array($shi_new)){
				$end_data = array_merge($cai_data,$shi_new);
			}else if(is_array($cai_data)){
				$end_data = $cai_data;
			}else if(is_array($shi_new)){
				$end_data = $shi_new;
			}else {
				$end_data = [];
			}
			$this->assign('searchContent',$searchContent);
		}
		$this->assign('end_data',$end_data);
		return $this->fetch();
	}
	public function searchData()
	{

	}
}
