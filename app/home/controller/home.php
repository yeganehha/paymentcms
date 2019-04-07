<?php


namespace App\home\controller;


use App\api\controller\factor;
use App\api\controller\service;
use paymentCms\component\request;

/**
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 3/23/2019
 * Time: 2:05 PM
 * project : paymentCms
 * virsion : 0.0.0.1
 * update Time : 3/23/2019 - 2:05 PM
 * Discription of this Page :
 */


if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


class home extends \controller {


	public function index($serviceId){
		$this->mold->offAutoCompile();
		if ( request::isPost() ){
			return $this->checkData($serviceId);
		}
		$service = service::info($serviceId);
		if ( ! $service['status'] ){
			$this->mold->offAutoCompile();
			\App\core\controller\httpErrorHandler::E404();
			return ;
		}
		$this->mold->set('service',$service['result']['service']);
		$this->mold->set('fields',$service['result']['fields']);
		$this->mold->view('home.mold.html');

	}

	public function checkData($serviceId){
		$this->mold->offAutoCompile();
		$result = factor::generate($serviceId,$_POST);
		show($result);
	}
	public function sd(){
		show(func_get_args());
	}
}