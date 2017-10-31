<?php
namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class Message extends Model
{
	use SoftDelete;
	protected $deleteTime = 'delete_time';

}