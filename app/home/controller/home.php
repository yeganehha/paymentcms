<?php


namespace App\home\controller;


use App\api\controller\service;
use App\invoice\controller\invoice;
use paymentCms\component\request;
use paymentCms\component\Response;

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
		$result = invoice::generate($serviceId,$_POST);
		if ( isset($result['link']) ) {
			$this->mold->offAutoCompile();
			Response::redirect($result['link']);
		} else {
			$this->alert('danger' , '',$result['massage']);
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
	}
}