<?php


namespace App\api\controller;


use paymentCms\component\file;
use paymentCms\component\Response;
use paymentCms\component\strings;

/**
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 3/29/2019
 * Time: 3:52 PM
 * project : paymentCMS
 * virsion : 0.0.0.1
 * update Time : 3/29/2019 - 3:52 PM
 * Discription of this Page :
 */


if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');



class innerController extends \controller {

	protected static $jsonResponse = true ;

	public function __construct() {
		$this->callHooks('adminHeaderNavbar',[1,2]);
	}

	protected function callHooks($hookName,$variable){
		$files = file::get_files_by_pattern(payment_path.'plugins'.DIRECTORY_SEPARATOR,'*'.DIRECTORY_SEPARATOR.'hook.php');
		foreach ($files as $file) {
			$temp = explode(DIRECTORY_SEPARATOR, strings::deleteWordLastString($file,DIRECTORY_SEPARATOR.'hook.php')) ;
			$class = 'plugin\\'.end($temp).'\hook';
			$method = '_'.$hookName;
			if ( method_exists($class,$method) ){
				$Object = new $class($this->mold);
				call_user_func_array([$Object,$method],$variable);
			}
		}
	}


	protected function jsonError($massage = null , $statusCode = 400){
		if ( isset($this) and self::$jsonResponse )
			Response::jsonError($massage,$statusCode);
		return false ;
	}

	protected function json($result){
		if ( isset($this) and self::$jsonResponse  )
			Response::json($result);
		return $result ;
	}
}