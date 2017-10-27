<?php
namespace app\index\controller;

use think\Controller;
use app\index\model\Book;
use app\index\model\User;
use app\index\model\BookInfo;
use app\index\model\Board;

class Index extends Controller
{
  	protected $book;
  	protected $user;
  	protected $board;
	public function _initialize()
	{
		$this->book = new Book();
		$this->user = new User();
		$this->board = new Board();
		$list = $this->board->where('parent_id',0)->select();
		$this->assign('board',$list);
	}
    public function index()
    {
    	return $this->fetch();
    }
    public function caipu()
    {
    	$board = $this->board->where('parent_id',4);
    	dump($this->request->param());
    	//$data = $this->book->where($where)->field('cid,uid,bookname,photo')->select();
    	//$data = $this->selectName($data);
    	//dump($data);
    	//$this->assign('data',$data);
    	//return $this->fetch();
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
	public function selectName($data)
	{
		$newdata = [];
		if(empty($data)){
			return false;
		}else{
			foreach ($data as $key => $value) {
				$arr = $value->toArray();
				$name = $this->user->where('uid',$value['uid'])->value('username');
				$arr['username'] = $name;
				$newdata[$key] = $arr;
			}
			return $newdata;
		}
	}
}
