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

use paymentCms\component\session;

if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');

define('PCVERSION' ,'1.0.0.1');


//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

error_reporting(0);
ini_set('display_startup_errors', 0);
ini_set('display_errors', false);

spl_autoload_register(function ($class_name_call) {
	if ( class_exists($class_name_call , false))
		return ;
	$class_name_call = trim($class_name_call);
	$paths = explode('\\' , $class_name_call) ;
	$class_name = array_pop($paths);
	$dire = array_shift($paths);
	if ( $dire == 'paymentCms' ) {
		$class_patch = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $paths) . DIRECTORY_SEPARATOR . $class_name . '.php';
	}elseif ( $dire == 'plugin' ) {
		Lang::addToLangfile($paths[0]);
		$class_patch = payment_path . 'plugins' . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $paths) . DIRECTORY_SEPARATOR . $class_name . '.php';
	}else {
		Lang::addToLangfile($paths[0]);
		$class_patch = payment_path . strtolower($dire) . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $paths) . DIRECTORY_SEPARATOR . $class_name . '.php';
	}
	$debug = debug_backtrace();
	if ( file_exists($class_patch)) {
		require_once $class_patch;
		if ( isset($debug[2]) and isset($debug[2]['function']) and $debug[2]['function'] == 'class_exists')
			return true ;
		if ( method_exists($class_name_call,'__init'))
			call_user_func([$class_name_call,'__init']);
	} else {
		if ( isset($debug[2]) and isset($debug[2]['function']) and $debug[2]['function'] == 'class_exists')
			return false ;
		App\core\controller\httpErrorHandler::E500($class_patch);
		exit;
	}
});


function shutdown() {
	$html = ob_get_contents();
	ob_clean();
	ob_end_clean();
	$error = error_get_last();
	if ($error != null and $error['type'] === E_ERROR) {
		$localData = array(
			'file' => str_replace(payment_path,'',$error['file']),'line' => $error['line'],'type' => $error['type'],'message' => str_replace(payment_path,'',$error['message']),
			'app_run' => App::getApp(),'app_provider' => App::getAppProvider(),'controller' => App::getController(),'method' => App::getMethod(),
			'server' => json_encode($_SERVER),'post' => json_encode($_POST),'get' => json_encode($_GET),'session' => json_encode(session::get()),'cookie' => json_encode($_COOKIE),'php' => phpversion(),'version' => PCVERSION ,'url' => \App::getFullRequestUrl(), 'app' => json_encode(\App::appsListWithConfig()),'siteUrl' => \App::getCurrentBaseLink(), 'lang' => 'fa', 'theme' => 'default',
		);
		curl("https://www.paymentcms.ir/api/report/bug",$localData);
		\App\core\controller\httpErrorHandler::E500($error['file']);
	}
	echo $html ;
	exit;
}
ob_start();
register_shutdown_function('shutdown');

require_once __DIR__ .DIRECTORY_SEPARATOR. 'app.php';
require_once __DIR__ .DIRECTORY_SEPARATOR. '..'.DIRECTORY_SEPARATOR.'component'.DIRECTORY_SEPARATOR.'controller.php';
require_once __DIR__ .DIRECTORY_SEPARATOR. '..'.DIRECTORY_SEPARATOR.'component'.DIRECTORY_SEPARATOR.'pluginController.php';
require_once payment_path. 'core'.DIRECTORY_SEPARATOR.'component'.DIRECTORY_SEPARATOR.'databaseConection.php';
require_once payment_path. 'core'.DIRECTORY_SEPARATOR.'component'.DIRECTORY_SEPARATOR.'lang.php';
require_once payment_path. 'core'.DIRECTORY_SEPARATOR.'component'.DIRECTORY_SEPARATOR.'function.php';
