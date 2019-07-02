<?php


namespace App\invoice\app_provider\api;


use App\core\controller\fieldService;
use App\invoice\model\transactions;
use paymentCms\component\model;
use paymentCms\component\request;
use paymentCms\component\security;
use paymentCms\component\strings;
use paymentCms\component\validate;

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


class invoice extends \App\api\controller\innerController {

	/**
	 * @param      $serviceId
	 * @param null $baseData
	 *
	 * @return array
	 */
	public static function generate($serviceId = null ,$baseData = null) {
		if (is_null($baseData) or !is_array($baseData)) $baseData = $_POST;
		$data = request::getFromArray($baseData, 'firstName,lastName,email,phone,price,description,customField,hookAction,returnTo');
		unset($baseData);
		if (  $serviceId != null ) {
			$tempJsonResult = self::$jsonResponse;
			self::$jsonResponse = false;
			$serviceResult = service::info($serviceId);
			self::$jsonResponse = $tempJsonResult;
			if ($serviceResult['status'] == false or (isset($serviceResult['result']) and $serviceResult['result'] == null)) {
				return self::jsonError('service not found!', 404);
			}
			$service = $serviceResult['result']['service'];
			$fields = $serviceResult['result']['fields'];
			unset($serviceResult);
		}


		if (  $serviceId != null ) {
			/* Validation start */
			if ($service['lastNameStatus'] == 'required') $rules['lastName'] = ['required', rlang('lastName')];
			if ($service['firstNameStatus'] == 'required') $rules['firstName'] = ['required', rlang('firstName')];
			if ($service['emailStatus'] == 'required' or ($service['emailStatus'] == 'visible' and $data['email'] != null)) $rules['email'] = ['required|email', rlang('email')];
			if ($service['phoneStatus'] == 'required' or ($service['phoneStatus'] == 'visible' and $data['phone'] != null)) $rules['phone'] = ['required|mobile', rlang('phone')];
			if (isset($rules)) {
				$valid = validate::check($data, $rules);
				if ($valid->isFail()) {
					return self::jsonError($valid->errorsIn());
				}
			}
			/* Validation end */
		} else {
			$rules['lastName'] = ['required', rlang('lastName')];
			$rules['firstName'] = ['required', rlang('firstName')];
			$rules['price'] = ['required|match:>100', rlang('price')];
			if ($data['email'] != null) $rules['email'] = ['required|email', rlang('email')];
			if ($data['phone'] != null) $rules['phone'] = ['required|mobile', rlang('phone')];
			if (isset($rules)) {
				$valid = validate::check($data, $rules);
				if ($valid->isFail()) {
					return self::jsonError($valid->errorsIn());
				}
			}
			$service['price'] = $data['price'];
			$service['description'] = $data['description'];
			$service['serviceId'] = null;
		}


		/* start getting user information */
		if ( $data['phone'] != null )
			$userSystem = [ 'value' => $data['phone']  , 'variable' => 'phone' ];
		elseif ( $data['email'] != null )
			$userSystem = [ 'value' => $data['email']  , 'variable' => 'email' ];
		if ( isset($userSystem) ){
			$userModel = \App\user\app_provider\api\user::getUser($userSystem['value'],$userSystem['variable'] . ' = ?');
		}
		/* end getting user information */

		model::transaction();
		$error = false ;
		/* @var \paymentCms\model\invoice $invoiceModel */
		$invoiceModel = self::model('invoice') ;
		$invoiceModel->setStatus('pending');
		$invoiceModel->setPrice($service['price']);
		$invoiceModel->setApiId(self::$api->getApiId());
		$invoiceModel->setBackUri($data['returnTo']);
		$invoiceModel->setCreatedIp(security::getIp() );
		$invoiceModel->setDueDate(date('Y-m-d H:i:s' , time()+4*24*60*60));
		$invoiceModel->setCreatedDate(date('Y-m-d H:i:s'));
		if ( isset($userModel) ) {
			if ( $userModel->getUserId() != null )
				$invoiceModel->setUserId($userModel->getUserId());
			else {
				$resultGenerateUser = \App\user\app_provider\api\user::generateUser(['fname'=>$data['firstName'],'lname'=>$data['lastName'],'email' => $data['email'],'phone'=>$data['phone'],'password'=>strings::generateRandomLowString(8),'groupId'=>1]);
				if ( $resultGenerateUser['status'] )
					$invoiceModel->setUserId( $resultGenerateUser['result']);
				else
					$error = $resultGenerateUser['massage'];
			}
		}
		$module = parent::callHooks('invoiceGateWays') ;
		if ( count($module) > 0 )
			$moduleSelect = array_keys($module)[0];
		else
			$moduleSelect = null ;

		$invoiceModel->setModule($moduleSelect);
		$invoiceModel->setRequestAction($data['hookAction']);
		$invoiceId = $invoiceModel->insertToDataBase();
		if ( $invoiceId !== false ){
			/* @var \paymentCms\model\items $itemsModel */
			$itemsModel = self::model('items') ;
			$itemsModel->setPrice($service['price']);
			$itemsModel->setServiceId($service['serviceId']);
			$itemsModel->setDescription($service['description']);
			$itemsModel->setTime(date('Y-m-d H:i:s'));
			$itemsModel->setInvoiceId($invoiceModel->getInvoiceId());
			$itemId = $itemsModel->insertToDataBase();
			if ( $itemId !== false ){
				if (  $serviceId != null ) {
					$resultFillOutForm = fieldService::fillOutForm($service['serviceId'], 'service', $data['customField'], $invoiceModel->getInvoiceId(), 'invoice');
					if (!$resultFillOutForm['status'])
						$error = $resultFillOutForm['massage'];
				}
			} else {
				$error = rlang('canNotInsertItems');
			}
		} else {
			$error = rlang('canNotInsertInvoice');
		}
		if ( $error === false ){
			model::commit();
			return self::json(['id' => $invoiceModel->getInvoiceId() , 'link' => self::generateUrlEncode($invoiceModel->getInvoiceId()) ]);
		} else {
			model::rollback();
			return self::jsonError($error,500);
		}
	}

	/**
	 * @param $invoiceId
	 *
	 * @return string
	 *               [no-access]
	 */
	public  static function generateUrlEncode($invoiceId){
		return \App::getBaseAppLink( 'invoice/'.urlencode(security::encrypt($invoiceId,'base64',true)) , 'invoice') ;
	}

	/**
	 * @param $transactionId
	 *
	 * @return string
	 *               [no-access]
	 */
	public  static function generateCallBackUrl($transactionId){
		return \App::getBaseAppLink( 'invoice/callBack/'.urlencode(security::encrypt($transactionId,'base64',true)) , 'invoice') ;
	}

	public static function startTransAction($invoice ){
		if ( is_int($invoice) ){
			/* @var \App\invoice\model\invoice $invoice */
			$invoice = self::model('invoice' , $invoice);
			if ( $invoice->getInvoiceId() == null ){
				return self::jsonError( rlang('canNotFindInvoice'),500);
			}
		}
		if ( $invoice->getStatus() == 'paid')
			return self::jsonError( rlang('invoicePaidBefore'),500);

		$moduleName = $invoice->getModule();
		model::transaction();
		$transaction = new transactions();
		$transaction->setModule($moduleName);
		$transaction->setIp(security::getIp());
		$transaction->setTime(date('Y-m-d H:i:s'));
		$transaction->setPrice($invoice->getPrice());
		$transaction->setStatus('pending');
		$transaction->setInvoiceId($invoice->getInvoiceId());
		if ( $transaction->insertToDataBase() ){
			/* @var \App\invoice\model\transactions $transaction */
			$module = self::callHooks($moduleName.'_startTransaction',[$transaction]);
			if ( count($module) == 0 ) {
				model::rollback();
				return self::jsonError( rlang('selectOtherGateWay'),500);
			}
			if ( $module[$moduleName]['status'] == false ){
				model::rollback();
				return self::jsonError( $module[$moduleName]['massage'],500);
			}
			$transaction->setTransactionCodeOne($module[$moduleName]['codeOne']);
			$transaction->setTransactionCodeTwo($module[$moduleName]['codeTwo']);
			$transaction->setDescription($module[$moduleName]['massage']);
			$transaction->upDateDataBase();
			model::commit();
			return self::json($module[$moduleName]) ;
		}
		model::rollback();
		return self::jsonError( rlang('cantInsertTransaction'),500);
	}

	public static function checkTransAction($transaction ){
		if ( is_int($transaction) ){
			/* @var \App\invoice\model\transactions $transaction */
			$transaction = self::model('transactions' , $transaction);
			if ( $transaction->getTransactionId() == null ){
				return self::jsonError( rlang('canNotFindTransaction'),500);
			}
		}
		/* @var \App\invoice\model\invoice $invoice */
		$invoice = self::model('invoice' , $transaction->getInvoiceId());
		if ( $invoice->getStatus() == 'paid')
			return self::jsonError( rlang('invoicePaidBefore'),500);

		$moduleName = $invoice->getModule();

		/* @var \App\invoice\model\transactions $transaction */
		$module = self::callHooks($moduleName.'_checkTransaction',[$transaction]);
		if ( count($module) == 0 ) {
			$transaction->setDescription(rlang('selectOtherGateWay'));
			$transaction->setStatus('feiled');
			$transaction->upDateDataBase();
			return self::jsonError( rlang('selectOtherGateWay'),500);
		}
		if ( $module[$moduleName]['status'] == false ){
			$transaction->setDescription( $module[$moduleName]['massage'] );
			$transaction->setStatus('feiled');
			$transaction->upDateDataBase();
			return self::jsonError( $module[$moduleName]['massage'],500);
		}
		$transaction->setTransactionCodeOne($module[$moduleName]['codeOne']);
		$transaction->setTransactionCodeTwo($module[$moduleName]['codeTwo']);
		$transaction->setDescription($module[$moduleName]['massage']);
		if ( $module[$moduleName]['payStatus'] ) {
			$transaction->setStatus('seucced');
			if ( $transaction->upDateDataBase() ) {
				$invoice->setPaidDate(date('Y-m-d H:i:s'));
				$invoice->setStatus('paid');
				if ( $invoice->upDateDataBase() ) {
					self::callHooks('paidInvoice' , [$invoice]);
					return self::json(null) ;
				} else {
					self::callHooks('paidInvoiceButErrorOccurred' , [$invoice]);
					return self::jsonError(rlang('callAdmin')) ;
				}
			} else {
				self::callHooks('paidInvoiceButErrorOccurred' , [$invoice]);
				return self::jsonError(rlang('callAdmin')) ;
			}
		} else {
			$transaction->setStatus($module[$moduleName]['payStatusType']);
			$transaction->upDateDataBase();
			return self::jsonError( $module[$moduleName]['massage'],500);
		}
	}
}