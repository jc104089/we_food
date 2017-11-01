<?php
namespace app\index\model;
use think\Model;
use traits\model\SoftDelete;
use app\index\model\LogInfo;

class Log extends Model
{
	use SoftDelete;
	protected $deleteTime = 'delete_time';
	public function logInfo()
	{
		return $this->hasOne('LogInfo' , 'l_id');
	}
	//热门日志
	public function hotLog()
	{
		$all_log = [];//存id
		$all_data = [];
        $data = LogInfo::field('l_id,img_url')->order('talknum desc')->select();
        if($data){
        	foreach ($data as $k => $v) {
	            $all_log[] = $v['l_id'];     
	        }
	        $log_data = $this->where('lid','in',$all_log)->where('status','neq',0)->limit(5)->select();
	        if($log_data){
	        	foreach ($log_data as $key => $value) {
	        		$value->logInfo;
	        		$value = $value->toArray();
	        		//$value['img_url'] = $value['logInfo']['img_url'];
	        		$all_data[$key] = $value;
	        	}
	        	return $all_data;
	        }else{
	        	return false;
	        }   
        }else{
        	return false;
        }  
	}
}