<?php


namespace App\api\controller;


use paymentCms\component\file;
use paymentCms\component\mold\Mold;
use paymentCms\component\request;
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



class innerController {

	protected static $jsonResponse = null ;
	protected static $api ;
	private static $mold;

	public function __construct() {
		if ( self::$jsonResponse == null ) {
			self::$mold = new Mold();
			self::$mold->offAutoCompile();
			/* @var \paymentCms\model\api $api */
			if (strings::strFirstHas(\app::getFullRequestUrl(), \app::getBaseAppLink(null, 'api'))) {
				$requestedIp = request::serverOne('REMOTE_ADDR');
				self::$jsonResponse = true;
			} else {
				$requestedIp = 'local';
				self::$jsonResponse = false;
			}
			$api = $this->model('api', "%" . $requestedIp . "%", ' ( allowIp Like ? or allowIp Like \'%*%\' ) and active = 1 limit 1');
			if ($api->getApiId() == null) {
				self::jsonError('Access Denied !', 403);
				Response::redirect(\App::getBaseAppLink('httpErrorHandler/403', 'core'));
				return false;
			}
			self::$api = $api;
			$this->callHooks('adminHeaderNavbar', [1, 2]);
		}
		return true ;
	}


	/**
	 * @param null   $model
	 * @param null   $searchVariable
	 * @param string $searchWhereClaus
	 *
	 * @return \App\model\model
	 */
	protected static function model($model  , $searchVariable = null , $searchWhereClaus = null) {
		$model = 'paymentCms\model\\'.$model ;
		if (class_exists($model)) {
			if ( $searchWhereClaus == null )
				return new $model($searchVariable) ;
			else
				return new $model($searchVariable,$searchWhereClaus) ;
		} else {
			\App\core\controller\httpErrorHandler::E500($model);
			exit;
		}

	}

	protected function callHooks($hookName,$variable){
		$files = file::get_files_by_pattern(payment_path.'plugins'.DIRECTORY_SEPARATOR,'*'.DIRECTORY_SEPARATOR.'hook.php');
		foreach ($files as $file) {
			$temp = explode(DIRECTORY_SEPARATOR, strings::deleteWordLastString($file,DIRECTORY_SEPARATOR.'hook.php')) ;
			$class = 'plugin\\'.end($temp).'\hook';
			$method = '_'.$hookName;
			if ( method_exists($class,$method) ){
				$Object = new $class(self::$mold);
				call_user_func_array([$Object,$method],$variable);
			}
		}
	}


	protected static function jsonError($massage = null , $statusCode = 400){
		if ( self::$jsonResponse )
			Response::jsonError($massage,$statusCode);
		return ['status'=> false , 'massage' => $massage ] ;
	}

	protected static function json($result){
		if ( self::$jsonResponse  )
			Response::json($result);
		return ['status'=> true , 'result' => $result ] ;
	}

	protected function getRealIp(){
		if(!empty(request::serverOne('HTTP_CLIENT_IP'))){
			//ip from share internet
			$ip = request::serverOne('HTTP_CLIENT_IP');
		}elseif(!empty( request::serverOne('HTTP_X_FORWARDED_FOR') )){
			//ip pass from proxy
			$ip = request::serverOne('HTTP_X_FORWARDED_FOR');
		}else{
			$ip = request::serverOne('REMOTE_ADDR');
		}
		return $ip;
	}
}