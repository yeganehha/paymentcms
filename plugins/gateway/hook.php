<?php


namespace plugin\gateway;


use App\invoice\app_provider\api\invoice;

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

	public function _adminHeaderNavbar(){
//		echo '<script>alert("ssssssss");</script>';
//		$this->mold->view('test.footer.mold.html');
	}

	public function _invoiceGateWays(){
		return rlang('gateWayTest') ;
	}


	public function _gateway_startTransaction($transaction){
		/* @var \App\invoice\model\transactions $transaction */
		$show['status'] = true ;
		$show['massage'] = 'send' ;
		$show['link'] = \App::getCurrentBaseLink('plugins/gateway/test.php');
		$show['type'] ='post';
		$show['inputs']['CallbackURL'] = invoice::generateCallBackUrl($transaction->getTransactionId()) ;
		$show['inputs']['price'] = $transaction->getPrice();
		$show['codeOne'] = time() ;
		$show['codeTwo'] = '' ;
		return $show;
	}
	public function _gateway_checkTransaction($transaction){
		/* @var \App\invoice\model\transactions $transaction */
		if($_GET['Status'] == 'OK'){
			$show['status'] = true ;
			$show['payStatus'] = true ;
			$show['massage'] = 'done' ;
			$show['codeOne'] = time() ;
			$show['codeTwo'] = $_GET['Authority']  ;
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