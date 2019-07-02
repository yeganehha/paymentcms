<?php
namespace App\invoice\controller;


/**
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 7/1/2019
 * Time: 9:23 PM
 * project : paymentCMS
 * virsion : 0.0.0.1
 * update Time : 7/1/2019 - 9:23 PM
 * Discription of this Page :
 */


use App\invoice\app_provider\api\invoice;
use App\invoice\app_provider\api\service;
use paymentCms\component\request;
use paymentCms\component\Response;

if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


class services extends \controller {

	public function index($serviceId){
		if ( request::isPost() ){
			return $this->checkData($serviceId);
		}
		$service = service::info($serviceId,$this->mold);
		if ( ! $service['status'] ){
			$this->mold->offAutoCompile();
			\App\core\controller\httpErrorHandler::E404();
			return ;
		}
		$this->mold->set('service',$service['result']['service']);
		$this->mold->view('home.mold.html');
	}

	public function checkData($serviceId){
		$result = invoice::generate($serviceId,$_POST);
		if ( $result['status'] ) {
			$this->mold->offAutoCompile();
			Response::redirect($result['result']['link']);
		} else {
			$this->alert('danger' , '',$result['massage']);
			$service = service::info($serviceId,$this->mold);
			if ( ! $service['status'] ){
				$this->mold->offAutoCompile();
				\App\core\controller\httpErrorHandler::E404();
				return ;
			}
			$this->mold->set('service',$service['result']['service']);
			$this->mold->view('home.mold.html');
		}
	}
}