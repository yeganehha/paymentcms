<?php


namespace App\invoice\controller;


use App\core\controller\fieldService;
use App\user\app_provider\api\user;
use paymentCms\component\request;
use paymentCms\component\Response;
use paymentCms\component\security;
use paymentCms\component\strings;

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

	public function index($base64InvoiceId,$modulePay = null){
		$invoiceId = security::decrypt(urldecode($base64InvoiceId),'base64');
		/* @var \App\invoice\model\invoice $invoice */
		$invoice = $this->model('invoice' , $invoiceId);
		if ( $invoice->getInvoiceId() == null ){
			$this->mold->offAutoCompile();
			\App\core\controller\httpErrorHandler::E404();
			return ;
		}
		if ( request::isPost("module") ){
			$module = request::postOne("module");
			$invoice->setModule($module);
			$invoice->upDateDataBase();
			Response::redirect(\App::getFullRequestUrl());
		}
		$servicesId = [];
		$items = $invoice->search($invoice->getInvoiceId(),'invoiceId = ?' ,'items' );
		if ( is_array($items) )
			$servicesId = array_column($items, 'serviceId');

		$allFields = fieldService::showFilledOutForm($servicesId , 'service' ,$invoice->getInvoiceId() , 'invoice' );

		$module = parent::callHooks('invoiceGateWays') ;
		$client  = user::getUserById($invoice->getUserId());
		$this->mold->set('client' , $client);
		$this->mold->set('module' , $module);
		$this->mold->set('invoice' , $invoice->returnAsArray());
		$this->mold->set('items' , $items);
		$this->mold->set('allFields' , $allFields['result']);
		$this->mold->path('default', 'invoice');
		$this->mold->view('invoiceClient.mold.html');
		$this->mold->setPageTitle(rlang('invoice'));
	}

}