<?php


namespace App\log ;

use App\core\controller\httpErrorHandler;
use App\user\app_provider\api\user;
use paymentCms\component\cache;
use paymentCms\component\model;
use paymentCms\component\request;
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
		$this->menu->addChild('configuration' ,'logs', rlang('logs' ) , \app::getBaseAppLink('logs','admin') , 'fa fa-history' );
	}

	public function _controllerStartToRun(){
		/* @var \App\log\model\log $log */
		$log = $this->model(['log','log']);
		$log->setMethod(\App::getMethod());
		$log->setAppProvider(\App::getAppProvider());
		$log->setApp(\App::getApp());
		$log->setController(\App::getController());
		$log->setCurrentUrl(\App::getFullRequestUrl());
		$log->setPreviousPage( request::serverOne('HTTP_REFERER','-'));
		$log->setLogName('view_webSite_page');
		$log->setDescription(rlang('view_webSite_page'));
		$userId = user::getUserLogin(true);
		$log->setUserId( $userId !== false ? $userId : null );
		$log->setBrowser();
		$log->setIp();
		$log->setActivityTime();
		$log->insertToDataBase();
	}



	public function _addLog($description , $log_name){
		/* @var \App\log\model\log $log */
		$log = $this->model(['log','log']);
		$log->setMethod(\App::getMethod());
		$log->setAppProvider(\App::getAppProvider());
		$log->setApp(\App::getApp());
		$log->setController(\App::getController());
		$log->setCurrentUrl(\App::getFullRequestUrl());
		$log->setPreviousPage( request::serverOne('HTTP_REFERER','-'));
		$log->setLogName($log_name);
		$log->setDescription($description);
		$userId = user::getUserLogin(true);
		$log->setUserId( $userId !== false ? $userId : null );
		$log->setBrowser();
		$log->setIp();
		$log->setActivityTime();
		$log->insertToDataBase();
	}

}