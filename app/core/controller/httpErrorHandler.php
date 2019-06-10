<?php

namespace App\core\controller;


use paymentCms\component\httpHeader;
use paymentCms\component\mold\Mold;

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
		ob_clean();
		$mold = new Mold();
		$mold->path('default', 'core');
		$mold->header('header.mold.html');
		$mold->footer('footer.mold.html');
		$mold->set('direction' , 'rtl');
		$mold->set('text_align' , 'right');
		$mold->set('float' , 'right');
		$mold->view('404.mold.html');
		httpHeader::generateStatusCodeHTTP(404);
	}
	public static  function E500($class_patch){
		ob_clean();
		exit;
		$mold = new Mold();
		$mold->path('default', 'core');
		$mold->header('header.mold.html');
		$mold->footer('footer.mold.html');
		$mold->set('direction' , 'rtl');
		$mold->set('text_align' , 'right');
		$mold->set('float' , 'right');
		$mold->view('500.mold.html');
		$mold->set('path',$class_patch);
		httpHeader::generateStatusCodeHTTP(500);
	}

	public static function E403() {
		ob_clean();
		$mold = new Mold();
		$mold->path('default', 'core');
		$mold->header('header.mold.html');
		$mold->footer('footer.mold.html');
		$mold->set('direction' , 'rtl');
		$mold->set('text_align' , 'right');
		$mold->set('float' , 'right');
		$mold->view('404.mold.html');
		httpHeader::generateStatusCodeHTTP(403);
	}
}