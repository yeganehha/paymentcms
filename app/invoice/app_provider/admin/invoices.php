<?php


namespace App\invoice\app_provider\admin;


use App\core\controller\fieldService;
use App\user\app_provider\api\user;
use paymentCms\component\model;
use paymentCms\component\request;
use paymentCms\component\Response;
use paymentCms\component\strings;
use paymentCms\component\validate;
use paymentCms\model\api;
use paymentCms\model\invoice;

/**
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 3/24/2019
 * Time: 10:15 AM
 * project : paymentCMS
 * virsion : 0.0.0.1
 * update Time : 3/24/2019 - 10:15 AM
 * Discription of this Page :
 */


if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


class invoices extends \controller {
	public function index($invoiceId){
		/* @var \paymentcms\model\invoice $invoice */
		$invoice = $this->model('invoice' , $invoiceId);
		if ( $invoice->getInvoiceId() == null ){
			$this->mold->offAutoCompile();
			\App\core\controller\httpErrorHandler::E404();
		    return ;
		}
		$servicesId = [];
		$items = $invoice->search($invoice->getInvoiceId(),'invoiceId = ?' ,'items' );
		if ( is_array($items) )
			$servicesId = array_column($items, 'serviceId');


		$allFields = fieldService::showFilledOutFormWithAllFields($servicesId,'service',$invoice->getInvoiceId() , 'invoice');

//		$this->mold->offAutoCompile();
//		show($allFields);
		$transactions = $invoice->search($invoice->getInvoiceId(),'invoiceId = ?' ,'transactions' );
		$this->mold->set('transactions' , $transactions);
		$client  = user::getUserById($invoice->getUserId());
		$this->mold->set('client' , $client);
		$this->mold->set('invoice' , $invoice->returnAsArray());
		$this->mold->set('items' , $items);
		$this->mold->set('allFields' , $allFields['result']);
		$this->mold->path('default', 'invoice');
		$this->mold->view('invoice.mold.html');
		$this->mold->setPageTitle(rlang('invoice'));
	}
	public function lists($status = 'all') {
		$get = request::post('page=1,perEachPage=25,name,description,price,link,active' ,null);
		$rules = [
			"page" => ["required|match:>0", rlang('page')],
			"perEachPage" => ["required|match:>0|match:<501", rlang('page')],
		];
		$valid = validate::check($get, $rules);
		$value = array( );
		$variable = array( );
		if ($valid->isFail()){
			//TODO:: add error is not valid data

		} else {
			if ( $get['name'] != null ) {
				$value[] = '%'.$get['name'].'%' ;
				$variable[] = 'name LIKE ?' ;
			}
			if ( $get['description'] != null ) {
				$value[] = '%'.$get['description'].'%' ;
				$variable[] = 'description LIKE ?' ;
			}
			if ( $get['price'] != null ) {
				$value[] = '%'.$get['price'].'%' ;
				$variable[] = 'price LIKE ?' ;
			}
			if ( $get['link'] != null ) {
				$value[] = '%'.$get['link'].'%' ;
				$variable[] = 'link LIKE ?' ;
			}
			if ( $get['active'] == 'active' ) {
				$value[] = '1' ;
				$variable[] = 'status = ?' ;
			}
		}
		if ( $status != 'all' and ( $status == 'pending' or  $status == 'canceled' or  $status == 'refused' or  $status == 'failed' or  $status == 'paid' )  ) {
			$value[] = $status ;
			$variable[] = 'status = ?' ;
		}
		$model = parent::model('invoice');
		$numberOfAll = ($model->search( (array) $value  , ( count($variable) == 0 ) ? null : implode('or' , $variable) , null, 'COUNT(invoiceId) as co' )) [0]['co'];
		$pagination = parent::pagination($numberOfAll,$get['page'],$get['perEachPage']);
		$search = $model->search( (array) $value  , ( ( count($variable) == 0 ) ? null : implode('or' , $variable) )  , null, '*'  , ['column' => 'invoiceId' , 'type' =>'desc'] , [$pagination['start'] , $pagination['limit'] ] );
		$this->mold->path('default', 'invoice');
		$this->mold->view('invoiceList.mold.html');
		$this->mold->setPageTitle(rlang('invoices'));
		$this->mold->set('activeMenu' , $status.'invoices');
		$this->mold->set('invoices' , $search);
	}

	public function deleteTransaction($transactionId){
		/* @var \App\invoice\model\transactions $transactions */
		$transactions = $this->model('transactions' , $transactionId);
		if ( $transactions->getTransactionId() == null ){
			$this->mold->offAutoCompile();
			\App\core\controller\httpErrorHandler::E404();
			return ;
		}
		$transactions->deleteFromDataBase();
		$this->alert('success' ,'' ,rlang('deletedTransaction'));
		Response::redirect(\App::getBaseAppLink('invoices/'.$transactions->getInvoiceId() , 'admin') );
	}

	public function deleteItem($itemId){
		/* @var \App\invoice\model\items $item */
		$item = $this->model('items' , $itemId);
		if ( $item->getItemId() == null ){
			$this->mold->offAutoCompile();
			\App\core\controller\httpErrorHandler::E404();
			return ;
		}
		/* @var \App\invoice\model\invoice $invoice */
		$invoice = $this->model('invoice' , $item->getInvoiceId());
		if ( $invoice->getInvoiceId() == null ){
			$this->mold->offAutoCompile();
			\App\core\controller\httpErrorHandler::E404();
			return ;
		}
		if ( $invoice->getStatus() != 'paid' ) {
			$invoice->setPrice($invoice->getPrice() - $item->getPrice() );
			if ( $invoice->getPrice()  == 0 )
				$invoice->setStatus('canceled') ;
			if ( $invoice->upDateDataBase() ) {
				$item->deleteFromDataBase();
			}
		}
		Response::redirect(\App::getBaseAppLink('invoices/'.$item->getInvoiceId() , 'admin') );
	}
}