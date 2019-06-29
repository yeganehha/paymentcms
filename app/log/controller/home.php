<?php
namespace App\log\controller ;

use App\core\controller\httpErrorHandler;

/**
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 5/29/2019
 * Time: 2:15 PM
 * project : paymentCMS
 * virsion : 0.0.0.1
 * update Time : 5/29/2019 - 2:15 PM
 * Discription of this Page :
 */


if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


class home extends \controller {
	public function index() {
		httpErrorHandler::E404();
	}
}