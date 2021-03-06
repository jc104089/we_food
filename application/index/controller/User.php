<?php
namespace app\index\controller;

use think\Controller;
use app\index\model\User as UserModel;
use app\index\model\UserInfo;
use app\index\model\Webset;
use app\index\model\Log;
use app\index\model\Book;
use app\index\model\LogInfo;
use app\index\model\BookInfo;
use app\index\model\Save;
use app\index\model\Link;
use  think\Session;
use  think\Cookie; 
use lib\Phoneyz;
use lib\SaeTClientV2;
use lib\SaeTOAuthV2;

class User extends Controller
{
	const WB_AKEY = '2771208683';
	const WB_SKEY = '87382c99a983252a222b40b2bf1bc541';
	const WB_CALLBACK_URL = 'http://food.userchao.top/index/User/weibo';
	protected $user;
	protected $log;
	protected $book;
	protected $o;
	protected $userinfo;
	public function _initialize()
	{
		$this->user = new UserModel();
		$this->userinfo = new UserInfo();
		$this->log = new Log();
		$this->book = new Book();
		$this->o = new SaeTOAuthV2(self::WB_AKEY , self::WB_SKEY);
		$link = Link::select();
		$webdata = Webset::find(1);
		$this->assign('webdata',$webdata);
		$this->assign('link',$link);
		
	}
	public function weibo()
	{
		if (isset($_REQUEST['code'])) {
			$keys = array();
			$keys['code'] = $_REQUEST['code'];
			$keys['redirect_uri'] = self::WB_CALLBACK_URL;
			try {
				$token = $this->o->getAccessToken( 'code', $keys ) ;
			} catch (OAuthException $e) {

			}
		}

		if ($token) {
			Session::set('token',$token);
			// setcookie( 'weibojs_'.$o->client_id, http_build_query($token) );
			Cookie::set('weibojs_'.$this->o->client_id, http_build_query($token));
		}
		$arr = Session::get('token');
		$c = new SaeTClientV2( self::WB_AKEY , self::WB_SKEY , $arr['access_token'] );
		// dump($c);
		$ms  = $c->home_timeline(); // done
		$uid_get = $c->get_uid();
		$uid = $uid_get['uid'];
		$user_message = $c->show_user_by_id( $uid);//
		// dump($user_message);
		//存入数据库
		$user_weibo = $user_message['id'];
		$user_name = $user_message['screen_name'];
		//dump($user_weibo);
		$user = $this->user->get(['user_weibo'=>$user_weibo]);
		//dump($user);die;
		if ($user) {
			Session::set('uid',$user->user_id);
			Session::set('username',$user->user_name);
			//$user_id = $user->user_id;
			//$userinfo = $this->userinfo->get(['u_id'=>$user_id]);
			//Session::set('userinfo_headi',$userinfo->userinfo_headi);
		}else{
			$this->user->save(['user_weibo'=>$user_weibo,'username'=>$user_name]);
			$user_id = $this->user->uid;
			Session::set('uid',$user_id);
			Session::set('username',$user_name);
			//$image = $user_message['profile_image_url'];
			// $image = Image::open($image);
			// //将图片裁剪为300x300并保存为crop.png
			// $haha = substr(str_shuffle(md5(str_shuffle('asdasd2423'))), 0, 8);
			// $image->save(ROOT_PATH . 'public' . DS . 'uploads/' . $haha .'.png');
			$newUser = $this->user->find($user_id);
			// 如果还没有关联数据 则进行新增
			//获取客户端ip
			$newUser->userInfo()->save(['utype'=>0]);
		 	//Session::set('userinfo_headi',$image);

		}
		$this->redirect('index/index');
	}
	// 注册页
	public function whiteInfo()
	{
		return $this->fetch();
	}
	// 登录页
	public function loginInfo()
	{
		$code_url = $this->o->getAuthorizeURL( self::WB_CALLBACK_URL );
		$this->assign('code_url',$code_url);
		return $this->fetch('user/loginnew');
	}
	//个人信息
	public function userdata()
	{
		$uid = $this->request->param('uid');
		if(empty($uid)){
			abort(404,'页面不存在');
		}else{
			$type     = $this->request->param('type');
			$type     = $type ? $type : 0;
			//查用户信息
			$u_info   = $this->user->find($uid);
			$u_info   = $this->changeOneData($u_info,'userInfo');
			//dump($u_info);die;
			// 查日志
			$log_data = $this->log->where('u_id',$uid)->where('status','neq',0)->paginate(6);
			$page_log = $log_data->render();
			$log_data = $this->changeMoreData($log_data,'logInfo','lid',true);
			$log_num  = count($log_data);
			//dump($log_data);die;
			// 查菜谱
			$cai_data = $this->book->where('uid',$uid)->where('status','neq',0)->paginate(6);
			$page_cai = $cai_data->render();
			$cai_data = $this->changeMoreData($cai_data,'bookInfo','cid');
			$cai_num  = count($cai_data);
			//dump($cai_data);die;
			// 查收藏
			$save_id   = Save::where('u_id',$uid)->paginate(6);
			$page_save = $save_id->render();
			$save_num  = count($save_id);
			$save_data = [];
			if($save_id){
				foreach ($save_id as $key => $value) {
					$one_data = $this->book->where('cid',$value['data_id'])->find();
					$one_data = $this->changeOneData($one_data,'bookInfo');
					$save_data[$key] = $one_data;
				}
			}			
			
			$this->assign([
				'type'       => $type,
				'u_info'     => $u_info,
				'log_data'   => $log_data,
				'log_num'    => $log_num,
				'cai_data'   => $cai_data,
				'cai_num'    => $cai_num,
				'save_num'   => $save_num,
				'save_data'  => $save_data,
				'page_log'   => $page_log,
				'page_cai'   => $page_cai,
				'page_save'  => $page_save,
			]);
			return $this->fetch();
		}
		
	}
	// 退出
	public function quit()
	{
		if(Session::has('uid')){
			Session::delete('uid');
			session::delete('username');
			$this->success('退出成功',url('index/index/index'));
		}else {
			$this->error('退出失败');
		} 
	}
    // 验证
    public function reg()
    {
    	$filed = ['username','password','phone','repwd','captcha'];
    	$arr = $this->request->param();
    	$key = key($arr);
    	//dump($arr[$key]);
    	if (is_array($key)){
    		$key = 'repwd';
    	}
    	$data = '';
    	if (in_array($key, $filed)){
	    	$result = $this->validate($arr,"User.$key");
			if(true !== $result){
			// 验证失败 输出错误信息
				$data = $result;
			} else {
				$data = '';
			}
		}
		if($key == 'username' && !empty($arr[$key])) { // 查看用户名是否重复
				$result = $this->user->where('username',$arr[$key])->find();
				if ($result) {
					$data = '该用户名已存在';
				}
		}
		if($key == 'phone' && !empty($arr[$key])) { // 查看用户名是否重复
				$result = $this->user->where('phone',$arr[$key])->find();
				if ($result) {
					$data = '该手机号已注册';
				}
		}
		if ($key == 'submit'){
			$data = '提交按钮';
		}
		echo json_encode($data);
    }
    //手机验证码
    public function phoneVer()
	{
		$phoneNum = $this->request->param('phone'); 
		//var_dump($_POST);
		if (empty($phoneNum)) {
			echo json_encode('手机号不能空');
		} else {
			$phoneyz = new Phoneyz($phoneNum);
			$phoneyz->getYzm();
			$code = $phoneyz->randNum;
			session('pcode',$code);
			echo $code;
		}
	}
	//注册用户
	public function addUser()
	{
		
		$arr = [
				'username' => $this->request->param('username'),
				'password' => md5($this->request->param('password')),
				'phone'    => $this->request->param('phone'),
		];
		
		$this->user->save($arr);
		$uid = $this->user->uid;
		$newUser = $this->user->find($uid);
		// 如果还没有关联数据 则进行新增
		//获取客户端ip
		$result = $newUser->userInfo()->save(['utype'=>0]);
		if ($this->user->uid && !empty($result)) {
			//echo json_encode('注册成功,请登录');
			$this->success('注册成功,请登录',url('index/index/index'));
		} else {
			//echo json_encode('注册失败');
			$this->error('注册失败');
		}
	}
	// 手机登录
	public function phoneLogin()
	{
		$data = $this->request->param('phone');
		$result = $this->user->where('phone',$data)->find();
		$id = $result->uid;
		$username = $result->username;
		if($id){
			session('uid',$id);
			session('username',$username);
			Webset::where('id',1)->setInc('visitnum',5);
			$this->error('登陆成功',url('index/center/info'));
			//echo json_encode($id);
		} else{
			$this->error('登陆失败');		}
		
	}
	//登录
    public function login()
    {
    	//$data = $this->request->param();
    	//dump($data);
    	$data['password'] = md5($this->request->param('password'));
    	$data['username'] = $this->request->param('username');
		$result = $this->user->where('username|phone',$data['username'])->where('password',$data['password'])->find();
		//dump($result);
		//dump($this->user->getLastSql());
		if ($result){
			$id = $result->uid;
			$username = $result->username;
			session('username',$username);
			session('uid',$id);
			Webset::where('id',1)->setInc('visitnum',5);
			$this->success('登陆成功',url('index/center/info'));
		} else {
			$this->error('登录失败');
		}
    }
	// 找回密码
	public function findPwd()
	{
		$data = $this->request->param('password');
		$phone = $this->request->param('phone');
		$data = md5($data);
		//dump($data);
		$result = $this->user->save(['password'=>$data],['phone'=>$phone]);
		//dump($result);
		if($result){
			$this->success('修改成功',url('index/user/logininfo'));
		}else{
			$this->error('手机号错误或未注册');
		}

	}
	public function changeMoreData($user,$ar,$id,$img = false)
	{
		$data = [];
		if(empty($user)){
			return false;
		} else {
			foreach ($user as $key => $value) {
				$value->$ar;
				$value = $value->toArray();
				//dump($value);die;
				$childInfo = array_pop($value);
				if(is_array($childInfo)){
					$all = array_merge($value,$childInfo);
				} else{
					$all = $value;
				}	
				//dump($all);die;
				if($img){
					if (strpos($all['img_url'],';')) {
						$all['oneimg_url'] = substr($all['img_url'], 0,strpos($all['img_url'],';'));
					}else{
						$all['oneimg_url'] = $all['img_url'];
					}
				}
				//dump($all);die;			
				$data[$all[$id]] = $all;
			}
			//dump($data);die;
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
	public function vip()
	{
		$uid = session('uid');
		if($uid){
		$res = $this->user->userInfo()->find($uid);
		$res = $res->toArray();
		$res = $res['utype'];
			if($res == 1){
				$data = $this->request->param('postcode');
				//配置您申请的appkey
				$appkey = "ebf414bf6e2424d87a17fabd223d5b88";
				//************1.邮编查询地名************
				$url = "http://v.juhe.cn/postcode/query";
				$params = array(
				      "postcode" => $data,//邮编，如：215001
				      "key" => $appkey,//应用APPKEY(应用详细页查询)
				      "page" => "",//页数，默认1
				      "pagesize" => 1,//每页返回，默认:20,最大不超过50
				      "dtype" => "",//返回数据的格式,xml或json，默认json
				);
				$paramstring = http_build_query($params);
				$content = $this->juhecurl($url,$paramstring);
				$result = json_decode($content,true);
				if($result){
				    if($result['error_code']=='0'){
				        print_r($result);
				    }else{
				        echo $result['error_code'].":".$result['reason'];
				    }
				}else{
				    echo "请求失败";
				}
				$result = $result['result']['list'][0];
				//dump($result);die;
				//dump($data);die;
				$this->assign('result',$result);
				return $this->fetch();
			}else{
				$this->error('您不是会员','center/vipcenter');
			}
			//dump($uid);dump($res);die;
			
			}
		
	}
	public function juhecurl($url,$params=false,$ispost=0){
	    $httpInfo = array();
	    $ch = curl_init();
	 
	    curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
	    curl_setopt( $ch, CURLOPT_USERAGENT , 'JuheData' );
	    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 60 );
	    curl_setopt( $ch, CURLOPT_TIMEOUT , 60);
	    curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	    if( $ispost )
	    {
	        curl_setopt( $ch , CURLOPT_POST , true );
	        curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
	        curl_setopt( $ch , CURLOPT_URL , $url );
	    }
	    else
	    {
	        if($params){
	            curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
	        }else{
	            curl_setopt( $ch , CURLOPT_URL , $url);
	        }
	    }
	    $response = curl_exec( $ch );
	    if ($response === FALSE) {
	        //echo "cURL Error: " . curl_error($ch);
	        return false;
	    }
	    $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
	    $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
	    curl_close( $ch );
	    return $response;
	}

}
