<?php


namespace App\invoice\controller;



use App\invoice\app_provider\api\service;
use paymentCms\component\request;
use paymentCms\component\Response;

/**
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 4/18/2019
 * Time: 1:18 PM
 * project : paymentCMS
 * virsion : 0.0.0.1
 * update Time : 4/18/2019 - 1:18 PM
 * Discription of this Page :
 */


if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


class home extends \controller {

	public function index($serviceLink = null){
		if ( request::isPost() ){
			return $this->checkData($serviceLink);
		}
		if ( $serviceLink != null ) {
			$service = service::info($serviceLink, $this->mold ,true);
			if (!$service['status']) {
				$this->mold->offAutoCompile();
				\App\core\controller\httpErrorHandler::E404();
				return;
			}
			$this->mold->set('service', $service['result']['service']);
		} else
			$this->mold->set('service', ['firstNameStatus' => 'required','lastNameStatus' => 'required','phoneStatus' => 'required']);
		$this->mold->view('home.mold.html');
	}

	public function checkData($serviceLink){
		if ( $serviceLink != null ) {
			$service = service::info($serviceLink, $this->mold, true);
			if (!$service['status']) {
				$this->mold->offAutoCompile();
				\App\core\controller\httpErrorHandler::E404();
				return;
			}
			$serviceId = $service['result']['service']['serviceId'];
		} else
			$serviceId = null ;
		$result = invoice::generate($serviceId,$_POST);
		if ( $result['status'] ) {
			$this->mold->offAutoCompile();
			Response::redirect($result['result']['link']);
		} else {
			$this->alert('danger' , '',$result['massage']);
			if ( $serviceLink != null )
				$this->mold->set('service',$service['result']['service']);
			else
				$this->mold->set('service', ['firstNameStatus' => 'required','lastNameStatus' => 'required','phoneStatus' => 'required']);
			$this->mold->view('home.mold.html');
		}
	}

}