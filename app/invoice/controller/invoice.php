<?php


namespace App\invoice\controller;


use App\core\controller\fieldService;
use App\user\app_provider\api\user;
use paymentCms\component\request;
use paymentCms\component\Response;
use paymentCms\component\security;


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



	public function index($base64InvoiceId,$modulePay = null){
		if ( $base64InvoiceId == 'callBack'){
			return $this->callBack($modulePay);
		}
		$invoiceId = security::decrypt(urldecode($base64InvoiceId),'base64');
		/* @var \App\invoice\model\invoice $invoice */
		$invoice = $this->model('invoice' , $invoiceId);
		if ( $invoice->getInvoiceId() == null ){
			$this->mold->offAutoCompile();
			\App\core\controller\httpErrorHandler::E404();
			return ;
		}
		if ( $modulePay != null ){
			$startTransaction = \App\invoice\app_provider\api\invoice::startTransAction($invoice);
			if ( $startTransaction['status']) {
				$this->mold->set('goToBank' , true);
				$this->mold->set('link' , $startTransaction['result']['link']);
				$this->mold->set('type' , $startTransaction['result']['type']);
				$this->mold->set('inputs' , $startTransaction['result']['inputs']);
			} else {
				self::alert('danger','',$startTransaction['massage']);
			}
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

		$transactions = $invoice->search($invoice->getInvoiceId(),'invoiceId = ?' ,'transactions' );

		$allFields = fieldService::showFilledOutForm($servicesId , 'service' ,$invoice->getInvoiceId() , 'invoice' );

		$module = parent::callHooks('invoiceGateWays') ;
		$client  = user::getUserById($invoice->getUserId());
		$this->mold->set('client' , $client);
		$this->mold->set('invoiceLink' , \App\invoice\app_provider\api\invoice::generateUrlEncode($invoice->getInvoiceId()));
		$this->mold->set('module' , $module);
		$this->mold->set('invoice' , $invoice->returnAsArray());
		$this->mold->set('items' , $items);
		$this->mold->set('transactions' , $transactions);
		$this->mold->set('allFields' , $allFields['result']);
		$this->mold->path('default', 'invoice');
		$this->mold->view('invoiceClient.mold.html');
		$this->mold->setPageTitle(rlang('invoice'));
	}


	/**
	 * @param $base64InvoiceId
	 *                        [global-access]
	 */
	public function callBack($base64InvoiceId){
		$transactionId = security::decrypt(urldecode($base64InvoiceId),'base64');
		/* @var \App\invoice\model\transactions $transaction */
		$transaction = $this->model('transactions' , $transactionId);
		if ( $transaction->getTransactionId() == null ){
			$this->mold->offAutoCompile();
			\App\core\controller\httpErrorHandler::E404();
			return ;
		}
		\App\invoice\app_provider\api\invoice::checkTransAction($transaction);
		Response::redirect(\App\invoice\app_provider\api\invoice::generateUrlEncode($transaction->getInvoiceId()));
	}

	public static function generate($serviceId = null ,$baseData = null) {
		return \App\invoice\app_provider\api\invoice::generate($serviceId,$baseData);
	}

}