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



//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

spl_autoload_register(function ($class_name_call) {
	if ( class_exists($class_name_call , false))
		return ;
	$class_name_call = trim($class_name_call);
	$paths = explode('\\' , $class_name_call) ;
	$class_name = array_pop($paths);
	$dire = array_shift($paths);
	if ( $dire == 'paymentCms' ) {
		$class_patch = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . strtolower(implode(DIRECTORY_SEPARATOR, $paths)) . DIRECTORY_SEPARATOR . $class_name . '.php';
	}elseif ( $dire == 'plugin' ) {
		Lang::addToLangfile($paths[0]);
		$class_patch = payment_path . 'plugins' . DIRECTORY_SEPARATOR . strtolower(implode(DIRECTORY_SEPARATOR, $paths)) . DIRECTORY_SEPARATOR . $class_name . '.php';
	}else {
		Lang::addToLangfile($paths[0]);
		$class_patch = payment_path . $dire . DIRECTORY_SEPARATOR . strtolower(implode(DIRECTORY_SEPARATOR, $paths)) . DIRECTORY_SEPARATOR . $class_name . '.php';
	}
	if ( file_exists($class_patch)) {
		require_once $class_patch;
		if ( method_exists($class_name_call,'__init'))
			call_user_func([$class_name_call,'__init']);
	} else {
		App\core\controller\httpErrorHandler::E500($class_patch);
		exit;
	}
});

function show($pram = null , $exit = true ){
	echo '<pre>';
	var_dump($pram);
	echo '</pre>';
	if ( $exit )
		exit;
}

require_once __DIR__ .DIRECTORY_SEPARATOR. 'app.php';
require_once __DIR__ .DIRECTORY_SEPARATOR. '..'.DIRECTORY_SEPARATOR.'component'.DIRECTORY_SEPARATOR.'controller.php';
require_once __DIR__ .DIRECTORY_SEPARATOR. '..'.DIRECTORY_SEPARATOR.'component'.DIRECTORY_SEPARATOR.'pluginController.php';
require_once payment_path. 'core'.DIRECTORY_SEPARATOR.'component'.DIRECTORY_SEPARATOR.'databaseConection.php';
require_once payment_path. 'core'.DIRECTORY_SEPARATOR.'component'.DIRECTORY_SEPARATOR.'lang.php';
