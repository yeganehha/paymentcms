<?php


namespace App\user\app_provider\admin;


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


class users extends \controller {
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

		$fieldsFill = [] ;
		$fieldsFillTemp = $invoice->search($invoice->getInvoiceId(),'invoiceId = ?' ,'fieldvalue' );
		if ( is_array($fieldsFillTemp) )
			foreach ( $fieldsFillTemp as $fieldFill) {
				$fieldsFill[ $fieldFill['fieldId'] ] = $fieldFill ;
			}
		unset($fieldsFillTemp);


		$searchFieldQuery = ' 0 ';
		$searchFieldVariable = [];
		if ( $servicesId != null ){
			$searchFieldQuery .= ' or serviceId IN ('.strings::deleteWordLastString(str_repeat('? , ',count($servicesId)),', ').')' ;
			$searchFieldVariable = array_merge($searchFieldVariable,$servicesId);
		}
		if ( array_keys($fieldsFill) != null ){
			$searchFieldQuery .= ' or fieldId IN ('.strings::deleteWordLastString(str_repeat('? , ',count(array_keys($fieldsFill))),', ').')' ;
			$searchFieldVariable = array_merge($searchFieldVariable,array_keys($fieldsFill));
		}
		$allFields =$invoice->search(array_filter($searchFieldVariable),$searchFieldQuery ,'field','*' ,['column'=>'orderNumber','type'=>'desc']);

		if ( is_array($allFields) )
			foreach ( $allFields as $index => $allField)
				if ( isset($fieldsFill[$allField['fieldId']]))
					$allFields[$index]['value'] = $fieldsFill[$allField['fieldId']]['value'] ;

//		$this->mold->offAutoCompile();
//		show($allFields);
		$this->mold->set('invoice' , $invoice->returnAsArray());
		$this->mold->set('items' , $items);
		$this->mold->set('allFields' , $allFields);
		$this->mold->path('default', 'invoice');
		$this->mold->view('invoice.mold.html');
		$this->mold->setPageTitle(rlang('invoice'));
	}
	public function lists() {
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
		$model = parent::model('invoice');
		$numberOfAll = ($model->search( (array) $value  , ( count($variable) == 0 ) ? null : implode('or' , $variable) , null, 'COUNT(invoiceId) as co' )) [0]['co'];
		$pagination = parent::pagination($numberOfAll,$get['page'],$get['perEachPage']);
		$search = $model->search( (array) $value  , ( ( count($variable) == 0 ) ? null : implode('or' , $variable) )  , null, '*'  , ['column' => 'invoiceId' , 'type' =>'desc'] , [$pagination['start'] , $pagination['limit'] ] );
		$this->mold->path('default', 'invoice');
		$this->mold->view('invoiceList.mold.html');
		$this->mold->setPageTitle(rlang('invoices'));
		$this->mold->set('activeMenu' , 'allinvoices');
		$this->mold->set('invoices' , $search);
	}

}