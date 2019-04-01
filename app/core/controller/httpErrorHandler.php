<?php

namespace App\core\controller;


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


class httpErrorHandler extends \controller {

	public function __construct() {

	}

	public static function E404(){
		echo '404';
		exit;
		parent::view('httpErrorHandler' , array('errorType' => '404'));
	}
	public static  function E500($class_patch){
		echo '500<br>'.$class_patch;
		exit;
		parent::view('httpErrorHandler' , array('errorType' => '500'));
	}

	public static function E403() {
		echo '403<br>';
		exit;
	}
}