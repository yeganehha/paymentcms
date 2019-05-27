<?php


namespace App\invoice\app_provider\admin;


use App\core\controller\fieldService;
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

}