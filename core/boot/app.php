<?php
/**
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 3/24/2019
 * Time: 7:58 PM
 * project : paymentCMS
 * version : 0.0.0.1
 * update Time : 3/24/2019 - 7:58 PM
 * Description of this Page :
 */


if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


class App {

	// default  controller and method if url is empty
	private static $app = 'home';
	private static $controller = 'home';
	private static $method = 'index';

	private static $params = [];
	private static $url = [];

	private static $appPatch = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR;

	public static function init() {

		self::generateUrlPrams();
		self::checkAppIsExist();
		self::checkControllerIsExist();
		self::checkMethodIsExist();
		self::getParamsFromUrl();
		$className ='App\\'.self::$app.'\controller\\'.self::$controller ;
		$methodName = self::$method ;
		if (class_exists($className) and method_exists($className, $methodName)) {
			$class = new $className ();
			call_user_func_array([$class, $methodName], self::$params);
		} else {
			$className ='App\core\controller\httpErrorHandler' ;
			$methodName = 'E404' ;
			$class = new $className ();
			call_user_func_array([$class, $methodName], self::$params);
		}

	}


	/**
	 * check is controller Exist
	 *
	 * @return bool
	 */
	private static function checkAppIsExist () {
		if ( !isset(self::$url[0]))
			return false ;
		$app = self::$url[0];
		if ( !empty($app)) {
			$app = trim(strtolower($app));
			$appPatch = self::$appPatch.$app;
			if (is_dir($appPatch)) {
				array_shift(self::$url);
				self::$app = $app;
				return true ;
			} else {
				return false ;
			}
		}
		return true ;
	}

	/**
	 * check is controller Exist
	 *
	 * @return bool
	 */
	private static function checkControllerIsExist () {
		if ( !isset(self::$url[0]))
			return false ;
		$controller = self::$url[0];
		if ( !empty($controller)) {
			$controller = trim(strtolower($controller));
			$controllerPatch = self::$appPatch.self::$app.DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . $controller . '.php' ;
			if (file_exists($controllerPatch)) {
				if (class_exists('App\\'.self::$app.'\controller\\'.$controller)) {
					array_shift(self::$url);
					self::$controller = $controller;
					return true ;
				} else {
					self::$app = 'core';
					self::$controller = 'httpErrorHandler';
					self::$method = 'E404';
					return false ;
				}
			} else {
				return false ;
			}
		}
		return true ;
	}


	/**
	 *
	 * @return bool
	 */
	private static function checkMethodIsExist(){
		if ( !isset(self::$url[0]))
			return false ;
		$method = self::$url[0];
		$className ='App\\'.self::$app.'\controller\\'.self::$controller ;
		if (class_exists($className)) {
			if ( method_exists($className,$method)) {
				array_shift(self::$url);
				self::$method = $method;
				return true;
			}else {
				return false ;
			}
		} else {
			return false ;
		}
	}


	private static function getParamsFromUrl(){
		self::$params = array_merge(self::$params , self::$url) ;
	}

	/**
	 * get url and generate to class and methods and prams
	 */
	private static function generateUrlPrams(){
		$url = [] ;
		if ( isset($_GET['urlFromHtaccess']) ){
			$url = explode('/' , trim($_GET['urlFromHtaccess']) );
		}
		self::$url = $url ;
	}

	/**
	 * @return string
	 */
	public static function getApp() {
		return self::$app;
	}



	public static function getFullRequestUrl(){
		$protocol = 'http';
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") $protocol = 'https';
		return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}

	public static function getAppPath($path = null , $app = null ){
		if ( $app == null)
			$app = self::$app ;
		$path = str_replace(['\\','/','>'],DIRECTORY_SEPARATOR,$path);
		$path = ( substr($path,-1) == DIRECTORY_SEPARATOR or is_null($path) ) ? $path : $path.DIRECTORY_SEPARATOR ;
		return payment_path.'app'.DIRECTORY_SEPARATOR.$app.DIRECTORY_SEPARATOR.(( ! is_null($path) ) ? $path : '' );
	}

	public static function getAppLink($path = null , $app = null ){
		$baseUrl = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
		$protocol = 'http';
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") $protocol = 'https';
		if ( $app == null)
			$app = self::$app ;
		$path = str_replace(['\\','/','>'],'/',$path);
		$path = str_replace('//','/',$path);
		$path = ( substr($path,-1) == DIRECTORY_SEPARATOR or is_null($path) ) ? $path : $path.'/' ;
		return $protocol. '://' . $_SERVER['HTTP_HOST'] .'/'.$baseUrl.'/app/'.$app.'/'.(( ! is_null($path) ) ? $path : '' );
	}
}