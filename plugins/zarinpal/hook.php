<?php


namespace plugin\zarinpal;


use App\invoice\app_provider\api\invoice;
use function Sodium\increment;

/**
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 3/26/2019
 * Time: 3:20 PM
 * project : paymentCMS
 * virsion : 0.0.0.1
 * update Time : 3/26/2019 - 3:20 PM
 * Discription of this Page :
 */


if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


class hook extends \pluginController {


	public function _invoiceGateWays(){
		return rlang('gateWayZarinPal') ;
	}

	public function _zarinpal_startTransaction($transaction){
		/* @var \App\invoice\model\transactions $transaction */
		$client = new \SoapClient('https://de.zarinpal.com/pg/services/WebGate/wsdl', array('encoding' => 'UTF-8'));
		$result = $client->PaymentRequest(
			array(
				'MerchantID' 	=> '51a47313-4db4-466e-9852-656b5ee8a9d4',
				'Amount' 	=> $transaction->getPrice(),
				'Description' 	=> 'pay invoice' .$transaction->getInvoiceId() .' - '.$transaction->getTransactionId(),
				'Email' 	=> null,
				'Mobile' 	=> null,
				'CallbackURL' 	=> invoice::generateCallBackUrl($transaction->getTransactionId())
			)
		);

		$result1=$result->Status ;
		if($result1 == 100)
		{
			$show['status'] = true ;
			$show['massage'] = 'send' ;
			$show['link'] ='https://www.zarinpal.com/pg/StartPay/'.$result->Authority;
			$show['type'] ='get';
			$show['inputs']['Test_name'] = 'value of thei fild for send to bank' ;
			$show['codeOne'] = '' ;
			$show['codeTwo'] = $result->Authority ;
		} else {
			$show['status'] = false ;
			$show['massage'] = $result1 ;
		}
		return $show;
	}
	public function _zarinpal_checkTransaction($transaction){
		/* @var \App\invoice\model\transactions $transaction */
		if($_GET['Status'] == 'OK'){
			$client = new \SoapClient('https://de.zarinpal.com/pg/services/WebGate/wsdl', array('encoding' => 'UTF-8'));
			$result = $client->PaymentVerification(
				array(
					'MerchantID'	 => '51a47313-4db4-466e-9852-656b5ee8a9d4' ,
					'Authority' 	 => $_GET['Authority'],
					'Amount'	 => $transaction->getPrice()
				)
			);
			$tracking_code = $result->RefID;
			$result=$result->Status ;

			if($result == 100){
				$show['status'] = true ;
				$show['payStatus'] = true ;
				$show['massage'] = 'done' ;
				$show['codeOne'] = $tracking_code ;
				$show['codeTwo'] = $_GET['Authority']  ;
			} else {
				$show['status'] = true ;
				$show['payStatus'] = false ;
				$show['payStatusType'] = 'feiled';
				$show['massage'] = 'ERROR!('.$result.')' ;
				$show['codeOne'] = $tracking_code ;
				$show['codeTwo'] = $_GET['Authority']  ;
			}
		} else {
			$show['status'] = true ;
			$show['payStatus'] = false ;
			$show['payStatusType'] = 'canceled';
			$show['massage'] =  'ERROR!(canceled)' ;
			$show['codeOne'] = null ;
			$show['codeTwo'] = $_GET['Authority']  ;
		}
		return $show;
	}
}