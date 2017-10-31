<?php
namespace app\index\controller;

use think\Controller;
use app\index\model\Book;
use app\index\model\User;
use app\index\model\UserInfo;
use app\index\model\BookInfo;
use app\index\model\Board;
use app\index\model\Log;
use app\index\model\LogInfo;
use app\index\model\Comment;
use  \think\Session;
use app\index\model\Praise;
use think\Db;

class Market extends Controller
{
	protected $book;
  	protected $user;
  	protected $board;
  	protected $log;
  	protected $userInfo;
  	protected $comment;
  	protected $parise;
	public function _initialize()
	{
		$this->book = new Book();
		$this->user = new User();
		$this->userInfo = new UserInfo();
		$this->board = new Board();
		$this->log = new Log();
		$this->comment = new Comment();
		$this->praise = new Praise();
	}
	public function rizhi()
	{
		//查日志
		$status = $this->request->param('status');
		$status = $status ? $status : 2;
		$data = $this->log->where('status',$status)->order('create_time desc')->paginate(2);
		$page = $data->render();
		$data = $this->changeMoreData($data,'logInfo','lid');
		//dump($data);
		//取日志中的一张图
		$log_data = [];
		foreach ($data as $key => $value) {
			if (strpos($value['img_url'],',')) {
				$value['img_url'] = substr($value['img_url'], 0,strpos($value['img_url'],','));
			}
			$log_data[$key] = $value;
		}
		//查菜谱收藏排行
		$all_id = [];
    	$cai_data = BookInfo::field('c_id')->order('savenum desc')->limit(6)->select();
    	foreach ($cai_data as $key => $value) {
    		$all_id[] = $value['c_id'];    		
    	}
    	$cai_data = $this->book->field('cid,uid,bookname,photo')->where('cid','in',$all_id)->select();
    	$this->assign('cai_data',$cai_data);
		$this->assign('page',$page);
		$this->assign('log_data',$log_data);
		return $this->fetch();
	}
	//日志详情页
	public function rizhixiang()
	{
		$lid = $this->request->param('lid');
		if(empty($lid)){
			abort(404,'页面不存在');
		}else{
			//日志详情
			$l_data = $this->log->where('lid',$lid)->find();
			$l_data = $this->changeOneData($l_data,'logInfo');
			//添加用户名和头像
			$name = $this->user->where('uid',$l_data['u_id'])->find();
			$l_data['username'] = $name->username;
			//dump($name);die;
			$l_data['photo'] = $name->userInfo->photo;
			//dump($l_data);
			//查回复
			$where['type_id'] = 1;
            $where['data_id'] = $lid;
            $reply_data = $this->comment->where($where)->order('create_time desc')->paginate(5);
            $page = $reply_data->render();
           // dump($page);die;
            $reply_num = $this->comment->where($where)->count();
            $reply_data = $this->selectName($reply_data,true);
           // dump($reply_data);die;
            //查点赞
             $praise_flag = 0;
            if(Session::has('uid')){
                $uid = Session::get('uid');
                $praise_data = $this->praise->where('type_id',1)->where('data_id',$lid)->where('uid',$uid)->find();
                if($praise_data){
                    $praise_flag = 1;
                }else {
                    $praise_flag = 0;
                }
            }
            //查菜谱收藏排行
            $all_id = [];
    		$cai_data = BookInfo::field('c_id')->order('savenum desc')->limit(6)->select();
    		foreach ($cai_data as $key => $value) {
    			$all_id[] = $value['c_id'];    		
    		}
    		$cai_data = $this->book->field('cid,uid,bookname,photo')->where('cid','in',$all_id)->select();
    		$this->assign('cai_data',$cai_data);
            $this->assign('praise_flag',$praise_flag);
            $this->assign('reply_data',$reply_data);
            $this->assign('reply_num',$reply_num);
            $this->assign('page',$page);
			$this->assign('l_data',$l_data);
			return $this->fetch();
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
    public function selectName($data,$pho=false)
	{
		$newdata = [];
		if(empty($data)){
			return false;
		}else{
			foreach ($data as $key => $value) {
				$arr = $value->toArray();
				$name = $this->user->find($value['uid']);
				$arr['username'] = $name->username;
                if($pho){
                    $name->userInfo;
                    $name = $name->toArray();
               // dump($name);
                $arr['photo'] = $name['userInfo']['photo'];
                }
                if($value['replyto'] != 0){
                	$arr['oldContent'] = $this->comment->where('tid',$value['totid'])->value('content');
                	$replyToName = $this->user->find($value['uid']);
					$arr['replyToName'] = $replyToName->username;
					$arr['replyToUid'] = $replyToName->uid;
                }
				$newdata[$key] = $arr;
			}
			return $newdata;
		}
	}
	//回复
	 public function saveComment()
    {
        $data = $this->request->param();
       // dump($data);
        if($data){
            $this->comment->data($data);
            $result = $this->comment->save();
            if($result){
            	$result2 = LogInfo::where('l_id',$data['data_id'])->setInc('talknum');
            	if($result2){
            		echo 1;
            	}else{
            		echo 0;
            	}
            }else {
                echo 0;
            }
        }else{
            echo 0;
        }

    }
    // 点赞
    public function clickgood()
    {
        $data = $this->request->param();
       // dump($data);
        if($data){
            $this->praise->data($data);
            $result1 = $this->praise->save();
            if($result1){
                $result2 = LogInfo::where('l_id',$data['data_id'])->setInc('goodnum');
                if($result2){
                     echo 1;
                 }else{
                    echo 0;
                 }               
            }else{
                echo 0;
            }
        }else{
            echo 0;
        }
    }
}