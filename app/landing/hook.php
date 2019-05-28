<?php


namespace App\landing;

use App\core\controller\httpErrorHandler;
use paymentCms\component\cache;
use paymentCms\component\model;
use paymentCms\component\Response;
use paymentCms\component\session;
use paymentCms\component\strings;
use pluginController;
use ReflectionClass;

/**
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 4/16/2019
 * Time: 11:16 AM
 * project : paymentCMS
 * virsion : 0.0.0.1
 * update Time : 4/16/2019 - 11:16 AM
 * Discription of this Page :
 */




if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


class hook extends pluginController {

	public function _adminHeaderNavbar($vars2){
		$this->menu->before('plugins','landingPages' , rlang(['list','pages'] ) , \app::getBaseAppLink('landingPage/lists') , 'fa fa-file' );
	}
}