<?php


namespace App\invoice\controller;


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

	public function index($base64InvoiceId){
		$invoiceId = base64_decode($base64InvoiceId);
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


		$searchFieldQuery = 'status != ? and status != ? and ( 0 ';
		$searchFieldVariable = ['admin' , 'invisible'];
		if ( $servicesId != null ){
			$searchFieldQuery .= ' or serviceId IN ('.strings::deleteWordLastString(str_repeat('? , ',count($servicesId)),', ').')' ;
			$searchFieldVariable = array_merge($searchFieldVariable,$servicesId);
		}
		if ( array_keys($fieldsFill) != null ){
			$searchFieldQuery .= ' or fieldId IN ('.strings::deleteWordLastString(str_repeat('? , ',count(array_keys($fieldsFill))),', ').')' ;
			$searchFieldVariable = array_merge($searchFieldVariable,array_keys($fieldsFill));
		}
		$searchFieldQuery .= ' ) ';
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
		$this->mold->view('invoiceClient.mold.html');
		$this->mold->setPageTitle(rlang('invoice'));
	}

}