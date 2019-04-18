<?php


namespace App\invoice\controller;



/**
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 4/1/2019
 * Time: 5:27 PM
 * project : paymentCMS
 * virsion : 0.0.0.1
 * update Time : 4/1/2019 - 5:27 PM
 * Discription of this Page :
 */


if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


class invoice extends \controller {

	public static function generate($serviceId,$baseData = null) {
		return \App\invoice\app_provider\api\invoice::generate($serviceId,$baseData);
	}

}