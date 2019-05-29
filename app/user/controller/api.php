<?php


namespace App\user\controller;


/**
 * [no-access]
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 5/29/2019
 * Time: 10:27 AM
 * project : paymentCMS
 * virsion : 0.0.0.1
 * update Time : 5/29/2019 - 10:27 AM
 * Discription of this Page :
 */


if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


class api extends \controller {

	public static function getAllUserAccessGroup() {
		/* @var \App\user\model\user_group $model */
		$model = (new api)->model(['user','user_group']);
		$access = $model->search(null,null);
		return $access ;
	}
}