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


use paymentCms\component\cache;
use paymentCms\component\file;
use paymentCms\component\model;
use paymentCms\component\strings;

if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


class App {

	// default  controller and method if url is empty
	private static $app = 'home';
	private static $appProvider = null;
	private static $controller = 'home';
	private static $method = 'index';

	private static $params = [];
	private static $url = [];

	private static $appPatch = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR;

	public static function init() {

		self::generateUrlPrams();
		$appName = self::$app ;

		$links = self::generateAllLinks();
		if ( is_array($links) ) {
			usort($links, ['App', 'sortArrayByLength']);
			$fullAddress = self::getFullRequestUrl();
			foreach ( $links as $link ){
				if ( strings::strFirstHas($fullAddress,$link['link']) ){
					if (  isset(self::$url[0])  and  strings::strLastHas($link['link'],'/'.self::$url[0]) ){
						$appName =  $link['app'];
						array_shift(self::$url);
					}
				}
			}
		} else {
			if (  isset(self::$url[0]) ) {
				$appName = self::$url[0];
				array_shift(self::$url);
			}
		}

		self::checkAppIsExist($appName);
		self::checkControllerIsExist();
		self::checkMethodIsExist();
		self::getParamsFromUrl();

		if ( self::$appProvider == null )
			$className ='App\\'.self::$app.'\controller\\'.self::$controller ;
		else
			$className ='App\\'.self::$appProvider.'\app_provider\\'.self::$app.'\\'.self::$controller ;
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
	private static function checkAppIsExist ($app = null ) {
		if ( $app == null )
			$app = self::$url[0];
		if ( !isset($app))
			return false ;
		if ( !empty($app)) {
			$app = trim(strtolower($app));
			$appPatch = self::$appPatch.$app;
			if (is_dir($appPatch)) {
//				array_shift(self::$url);
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
		$controller = trim(self::$url[0]);
		if ( !empty($controller)) {
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
			} elseif ( is_dir(self::$appPatch.self::$app )) {
				$files = file::get_files_by_pattern(self::$appPatch,'*'.DIRECTORY_SEPARATOR.'app_provider'.DIRECTORY_SEPARATOR.self::$app.DIRECTORY_SEPARATOR.$controller.'.php');
				if ( is_array($files) and count($files) > 0 ){
					$appProvider = strings::deleteWordFirstString(strings::deleteWordLastString($files[0] ,DIRECTORY_SEPARATOR.'app_provider'.DIRECTORY_SEPARATOR.self::$app.DIRECTORY_SEPARATOR.$controller.'.php'),self::$appPatch);
					if (class_exists('App\\'.$appProvider.'\app_provider\\'.self::$app.'\\'.$controller)) {
						array_shift(self::$url);
						self::$controller = $controller;
						self::$appProvider = $appProvider;
						return true ;
					}
				} else
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
		if ( self::$appProvider == null )
			$className ='App\\'.self::$app.'\controller\\'.self::$controller ;
		else
			$className ='App\\'.self::$appProvider.'\app_provider\\'.self::$app.'\\'.self::$controller ;
		if (class_exists($className,false)) {
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

	public static function getAppProvider() {
		return self::$appProvider;
	}


	public static function appsList(){
		return file::get_name_folders(self::$appPatch);
	}

	public static function appsControllerList($app = null){
		$result = [];
		if ( $app != null ){
			$result1 = file::get_name_file(self::$appPatch.$app.DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR,false,[],['.php']);
			$result2 = file::get_name_file_by_pattern(self::$appPatch,false,'*'.DIRECTORY_SEPARATOR.'app_provider'.DIRECTORY_SEPARATOR.$app.DIRECTORY_SEPARATOR.'*.php');
			return array_merge($result1,$result2);
		} else {
			$apps = self::appsList();
			if ( is_array($apps) ){
				foreach ($apps as $app ){
					$result1 =  file::get_name_file(self::$appPatch.$app.DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR,false,[],['.php']);
					$result2 = file::get_name_file_by_pattern(self::$appPatch,false,'*'.DIRECTORY_SEPARATOR.'app_provider'.DIRECTORY_SEPARATOR.$app.DIRECTORY_SEPARATOR.'*.php');
					$result[$app] = array_merge($result1,$result2);
				}
			}
		}
		return array_filter($result) ;
	}

	public static function appsControllerMethodList($app = null , $controller = null ){
		$return = [];
		if ( $app != null and $controller != null ){
			if ( is_file(self::$appPatch.$app.DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.$controller.'.php') ){
				$methods =  get_class_methods( 'App\\'.$app.'\controller\\'.$controller );
				for ( $i = count($methods) -1  ; $i >= 0 ; $i-- )
					if ( strings::strFirstHas($methods[$i],'__'))
						unset($methods[$i]);
					if ( count($methods) == 0 )
						return false ;
				return [ 'app' => $app ,'appProvider' => '', 'controller' => $controller,'methods' =>$methods];
			} else {
				$files = file::get_files_by_pattern(self::$appPatch,'*'.DIRECTORY_SEPARATOR.'app_provider'.DIRECTORY_SEPARATOR.$app.DIRECTORY_SEPARATOR.$controller.'.php');
				if ( is_array($files) and count($files) > 0 ){
					$appProvider = strings::deleteWordFirstString(strings::deleteWordLastString($files[0] ,DIRECTORY_SEPARATOR.'app_provider'.DIRECTORY_SEPARATOR.self::$app.DIRECTORY_SEPARATOR.$controller.'.php'),self::$appPatch);
					$methods =  get_class_methods( 'App\\'.$appProvider.'\app_provider\\'.$app.'\\'.$controller) ;
					for ( $i = count($methods) -1  ; $i >= 0 ; $i-- )
						if ( strings::strFirstHas($methods[$i],'__'))
							unset($methods[$i]);
					if ( count($methods) == 0 )
						return false ;
					return [ 'app' => $app ,'appProvider' => $appProvider, 'controller' => $controller,'methods' => $methods];
				}
			}
		} elseif ( $app != null and $controller == null ){
			$controllers = self::appsControllerList($app);
			foreach ($controllers as $tempController ){
				$result =self::appsControllerMethodList($app,$tempController) ;
				if ( $result != false )
					$return[$tempController] = $result ;
			}
		} else {
			$apps = self::appsList();
			foreach ($apps as $tmpApp ){
				$result =self::appsControllerMethodList($tmpApp) ;
				if ( $result != false )
					$return[$tmpApp] = $result ;
			}
		}
		return $return ;
	}

	public static function getFullRequestUrl(){
		$protocol = 'http';
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") $protocol = 'https';
		return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}

	public static function getAppPath($path = null , $app = null ){
		$baseDir = 'app';
		if ( $app == null)
			$app = self::$app ;
		elseif ( strings::strLastHas($app,':plugin')){
			$baseDir = 'plugins';
			$app = strings::deleteWordLastString($app,':plugin');
		}
		$path = str_replace(['\\','/','>'],DIRECTORY_SEPARATOR,$path);
		$path = ( substr($path,-1) == DIRECTORY_SEPARATOR or is_null($path) ) ? $path : $path.DIRECTORY_SEPARATOR ;
		return payment_path.$baseDir.DIRECTORY_SEPARATOR.$app.DIRECTORY_SEPARATOR.(( ! is_null($path) ) ? $path : '' );
	}

	public static function getAppLink($path = null , $app = null ){
		$baseUrl = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
		$protocol = 'http';
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") $protocol = 'https';
		$baseDir = 'app';
		if ( $app == null)
			$app = self::$app ;
		elseif ( strings::strLastHas($app,':plugin')){
			$baseDir = 'plugins';
			$app = strings::deleteWordLastString($app,':plugin');
		}
		$path = str_replace(['\\','/','>'],'/',$path);
		$path = str_replace('//','/',$path);
		$path = ( substr($path,-1) != '/' and  ! is_null($path)) ? $path : $path.'/' ;
		return $protocol. '://' . $_SERVER['HTTP_HOST'] .$baseUrl.$baseDir.'/'.$app.'/'.(( ! is_null($path) ) ? $path : '' );
	}

	public static function getBaseAppLink($path = null , $app = null ){
		if ( $app == null)
			$app = self::$app ;
		$baseUrl = self::getAppLinkFromAppsLink($app);
		$path = str_replace(['\\','/','>'],'/',$path);
		$path = str_replace('//','/',$path);
		$path = ( substr($path,-1) != '/' and  ! is_null($path)) ? $path : $path.'/' ;
		return $baseUrl.'/'.(( ! is_null($path) ) ? $path : '' );
	}

	private static function getAppLinkFromAppsLink($app){
		$links = self::generateAllLinks();
		$key = array_search($app, array_column($links, 'app'));
		return $links[$key]['link'];
	}

	private static function generateAllLinks(){
		if ( ! cache::hasLifeTime('apps_link','paymentCms')) {
			$links = model::searching(null, null, 'apps_link');
			cache::save($links, 'apps_link', PHP_INT_MAX, 'paymentCms');
		} else {
			$links = cache::get('apps_link',null,'paymentCms');
		}
		return $links ;
	}
	private static function sortArrayByLength($a,$b){
		return strlen($b['link'])-strlen($a['link']);
	}
}