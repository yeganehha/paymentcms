<?php


namespace App\api\controller;


use paymentCms\component\cache;
use paymentCms\component\file;
use paymentCms\component\menu\menu;
use paymentCms\component\model;
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


	public static function __init(){
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
			$api = self::model('api', ["%" . $requestedIp . "%"], ' ( allowIp Like ? or allowIp Like \'%*%\' ) and active = 1');
			if ($api->getApiId() == null) {
				self::jsonError('Access Denied !', 403);
				Response::redirect(\App::getBaseAppLink('httpErrorHandler/403', 'core'));
				return false;
			}
			self::$api = $api;
			self::callHooks('adminHeaderNavbar', [1, 2]);
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
	protected static function model($modelName  , $searchVariable = null , $searchWhereClaus = null) {
		if (empty(\app::getAppProvider()))
			$model = 'App\\' . \app::getApp() . '\model\\' . $modelName;
		else
			$model = 'App\\' . \app::getAppProvider() . '\model\\' . $modelName;
		if (class_exists($model)) {
			if ($searchWhereClaus == null) return new $model($searchVariable); else
				return new $model($searchVariable, $searchWhereClaus);
		} else {
			$model = 'paymentCms\model\\' . $modelName;
			if (class_exists($model)) {
				if ($searchWhereClaus == null) return new $model($searchVariable); else
					return new $model($searchVariable, $searchWhereClaus);
			} else {
				\App\core\controller\httpErrorHandler::E500($model);
				exit;
			}
		}
	}

	/**
	 * @param $hookName
	 * @param $variable
	 *
	 * @return array
	 */
	protected static function callHooks($hookName,$variable = null){
		$files = [];
		$appsActives = cache::get('appStatus', null  ,'paymentCms');
		if ( is_array($appsActives) and ! empty($appsActives) ) {
			foreach ($appsActives as $appName => $appStatus) {
				if ($appStatus == 'active') {
					if ( is_file(payment_path . 'app' . DIRECTORY_SEPARATOR.$appName. DIRECTORY_SEPARATOR . 'hook.php') ) {
						$files[] = [ 'aria' => 'app' , 'controller' => $appName ];
					}
				}
			}
		}
		$pluginSActives = cache::get('pluginStatus', null  ,'paymentCms');
		if ( is_array($pluginSActives) and ! empty($pluginSActives) ) {
			foreach ($pluginSActives as $pluginName => $pluginStatus) {
				if ($pluginStatus == 'active') {
					if ( is_file(payment_path . 'plugins' . DIRECTORY_SEPARATOR.$pluginName. DIRECTORY_SEPARATOR . 'hook.php') ) {
						$files[] = [ 'aria' => 'plugin' , 'controller' => $pluginName ];
					}
				}
			}
		}
		$menu = new menu('api');
		$return = [] ;
		foreach ($files as $file) {
			$controller = $file['controller'];
			$aria = $file['aria'] ;
			$class = $aria.'\\'.$controller.'\hook';
			$method = '_'.$hookName;
			if ( method_exists($class,$method) ){
				/* @var \paymentCms\component\mold\Mold $mold */
				$mold = self::$mold;
				if ( $aria == 'plugin')
					$mold->path(null,$controller.':plugin');
				else
					$mold->path(null,$controller);
				$Object = new $class($mold,$menu);
				$return[$controller] = call_user_func_array([$Object,$method],$variable);
			}
		}
		return $return ;
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