<?php
namespace App\install\controller;

use paymentCms\component\mold\Mold;

/**
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 5/30/2019
 * Time: 10:35 PM
 * project : paymentCMS
 * virsion : 0.0.0.1
 * update Time : 5/30/2019 - 10:35 PM
 * Discription of this Page :
 */


if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


class home {

	public static function index() {
		$mold = new Mold();
		$mold->path('default' ,'install');
		$mold->view('step1.mold.html');
		$mold->unshow('footer.mold.html' ,'header.mold.html');
	}
}