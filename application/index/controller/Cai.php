<?php
namespace app\index\controller;

use think\Controller;
use app\index\model\Book;
use app\index\model\User;
use app\index\model\BookInfo;
use app\index\model\Board;
use app\index\model\Comment;
use app\index\model\Praise;
use app\index\model\Save;
use app\index\model\Log;
use app\index\model\LogInfo;
use app\index\model\Link;
use app\index\model\Webset;
use  \think\Session;

class Cai extends Controller
{
	protected $book;
  	protected $user;
    protected $board;
    protected $comment;
    protected $parise;
    protected $saveCai;
  	protected $log;
	public function _initialize()
	{
		$this->book = new Book();
		$this->user = new User();
        $this->board = new Board();
        $this->comment = new Comment();
        $this->praise = new Praise();
        $this->saveCai = new Save();
		$this->log = new Log();
        $link = Link::select();
        $webdata = Webset::find(1);
        $this->assign('webdata',$webdata);
        $this->assign('link',$link);
	}
    //菜谱首页
	public function caipu()
    {
    	$all_classify = $this->board->allClass();
    	// 轮播
    	$all_id = [];
    	$scroll_data = BookInfo::field('c_id')->order('goodnum desc')->limit(6)->select();
    	foreach ($scroll_data as $key => $value) {
    		$all_id[] = $value['c_id'];    		
    	}
    	$scroll_data = $this->book->field('cid,uid,bookname,photo')->where('cid','in',$all_id)->select();
    	//dump($scroll_data);die;
    	//查找分类版块
    	$board = $this->board->boardClass();
    	//查找对应版块内容
    	$status = $this->request->param('status');
    	//dump(is_null($status));
    	$status = is_null($status) ? 0 : $status;
    	$data = $this->book->boardData($status);
    	$data = $this->selectName($data);
      //  dump($this->book->getLastSql());
    	//dump($data);die;
    	$this->assign('scroll_data',$scroll_data);
    	$this->assign('all_classify',$all_classify);
    	$this->assign('status',$status);
    	$this->assign('data',$data);
    	$this->assign('board',$board);
    	return $this->fetch();
    }
    // 菜谱排行
    public function hotcai()
    {
    	$all_classify = $this->board->allClass();
    	$all_id = [];
    	$data = BookInfo::field('c_id')->order('goodnum desc')->select();
    	foreach ($data as $key => $value) {
    		$all_id[] = $value['c_id'];    		
    	}
    	$data = $this->book->field('cid,uid,bookname,photo,status')->where('cid','in',$all_id)->paginate(2);
    	$page = $data->render();
    	$data = $this->selectName($data);
    	/*dump($page);
    	dump($data);die;*/
    	//查找分类版块
    	$board = $this->board->boardClass();
        //查找日志热门
        
        $log_data = $this->log->hotLog();
       // dump($log_data);die;
        $this->assign('log_data',$log_data);
    	$this->assign('page',$page);
    	$this->assign('all_classify',$all_classify);
    	$this->assign('board',$board);
    	$this->assign('data',$data);
    	return $this->fetch();
    }
    //菜谱详情
    public function recipe()
    {
        $cid = $this->request->param('cid');
        if(empty($cid)){
           // echo '没参数';
            abort(404,'页面不存在');
        }else{
            $all_classify = $this->board->allClass();
            //查找分类版块
             $board = $this->board->boardClass();
            // 查菜谱详情
            $cai_data = $this->book->where('cid',$cid)->find();
           // $cai_data = $this->book->bookInfo;
            $cai_data = $this->changeOneData($cai_data,'bookInfo');
            $name = $this->user->where('uid',$cai_data['uid'])->value('username');
            $cai_data['username'] = $name;
            // 菜谱分类
            $cai_type = $this->board->where('id','in',$cai_data['board_id'])->select();
            //dump($cai_type);
            //dump($cai_data);die;
            // 最受欢迎菜谱
            $all_id = [];
            $scroll_data = BookInfo::field('c_id')->order('goodnum desc')->limit(9)->select();
            foreach ($scroll_data as $key => $value) {
                $all_id[] = $value['c_id'];         
            }
            $scroll_data = $this->book->field('cid,uid,bookname,photo,status')->where('cid','in',$all_id)->select();
            //查回复
            $where['type_id'] = 0;
            $where['data_id'] = $cid;
            $reply_data = $this->comment->where($where)->order('create_time desc')->paginate(5);
            $page = $reply_data->render();
           // dump($page);die;
            $reply_num = $this->comment->where($where)->count();
            $reply_data = $this->selectName($reply_data,true);
            //点赞收藏
            $praise_flag = 0;
            if(Session::has('uid')){
                $uid = Session::get('uid');
                $praise_data = $this->praise->where('type_id',0)->where('data_id',$cid)->where('uid',$uid)->find();
                if($praise_data){
                    $praise_flag = 1;
                }else {
                    $praise_flag = 0;
                }
            }
           $save_flag = 0;
            if(Session::has('uid')){
                $uid = Session::get('uid');
                $save_data = $this->saveCai->where('data_id',$cid)->where('u_id',$uid)->find();
                if($save_data){
                    $save_flag = 1;
                }else {
                    $save_flag = 0;
                }
            }


            $this->assign([
                    'save_flag' => $save_flag,
                    'praise_flag'=>$praise_flag,
                    'page'=>$page,
                    'reply_num'=>$reply_num,
                    'reply_data'=>$reply_data,
                    'scroll_data'=>$scroll_data,
                    'cai_type'=>$cai_type,
                    'cai_data'=>$cai_data,
                    'all_classify'=>$all_classify,
                    'board'=>$board
            ]);
            return $this->fetch();
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
                
				$newdata[$key] = $arr;
			}
			return $newdata;
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
    public function saveComment()
    {
        $data = $this->request->param();
       // dump($data);
        if($data){
            $this->comment->data($data);
            $result = $this->comment->save();
            if($result){
                $result2 = BookInfo::where('c_id',$data['data_id'])->setInc('talknum');
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
    public function clickgood()
    {
        $data = $this->request->param();
       // dump($data);
        if($data){
            $this->praise->data($data);
            $result1 = $this->praise->save();
            if($result1){
                $result2 = BookInfo::where('c_id',$data['data_id'])->setInc('goodnum');
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
    public function clicksave()
    {
        $data = $this->request->param();
       // dump($data);
        if($data){
            $this->saveCai->data($data);
            $result1 = $this->saveCai->save();
            if($result1){
                $result2 = BookInfo::where('c_id',$data['data_id'])->setInc('savenum');
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