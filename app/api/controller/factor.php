<?php


namespace App\api\controller;


use paymentCms\component\request;
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


class factor extends innerController {

	public static function generate($serviceId,$baseData = null) {
		if ( self::$api == null )
			self::init();
		if (is_null($baseData) or !is_array($baseData)) $baseData = $_POST;
		$data = request::getFromArray($baseData, 'firstName,lastName,email,phone,price,description,customField');
		unset($baseData);
		self::$jsonResponse = false;
		$serviceResult = service::info($serviceId);
		self::$jsonResponse = true;
		if ($serviceResult['status'] == false or (isset($serviceResult['result']) and $serviceResult['result'] == null)) {
			return self::jsonError('service not found!', 404);
		}
		$service = $serviceResult['result']['service'];
		$fields = $serviceResult['result']['fields'];
		unset($serviceResult);


		/* Validation start */
		if ($service['lastNameStatus'] == 'required') $rules['lastName'] = ['required', rlang('lastName')];
		if ($service['firstNameStatus'] == 'required') $rules['firstName'] = ['required', rlang('firstName')];
		if ($service['emailStatus'] == 'required' or ($service['emailStatus'] == 'visible' and $data['email'] != null)) $rules['email'] = ['required|email', rlang('email')];
		if ($service['phoneStatus'] == 'required' or ($service['phoneStatus'] == 'visible' and $data['phone'] != null)) $rules['phone'] = ['required|mobile', rlang('phone')];
		if (!empty($fields) and is_array($fields)) {
			foreach ($fields as $key => $field) {
				$regix = null;
				if ($field['regex'] != null) {
					$regix = explode(',', $field['regex']);
				}
				if ($field['status'] == 'required') {
					$regix[] = 'required';
				}
				if ($regix == null or count($regix) == 0) continue;
				$rules['customField.' . $field['fieldId']] = [implode('|', array_unique(array_filter($regix))), $field['title']];
			}
		}
		if (isset($rules)) {
			$valid = validate::check($data, $rules);
			if ($valid->isFail()) {
				return self::jsonError($valid->errorsIn());
			}
		}
		/* Validation end */


		/* start getting user information */
		if ( $data['phone'] != null )
			$userSystem = [ 'value' => $data['phone']  , 'variable' => 'phone' ];
		elseif ( $data['email'] != null )
			$userSystem = [ 'value' => $data['email']  , 'variable' => 'email' ];
		if ( isset($userSystem) ){
			/* @var \paymentCms\model\user $userModel */
			$userModel = self::model('user',$userSystem['value'] , $userSystem['variable'] .' = ? order by userId limit 1') ;
		}
		/* end getting user information */


		\database::startTransaction();
		$error = false ;
		/* @var \paymentCms\model\factor $factorModel */
		$factorModel = self::model('factor') ;
		$factorModel->setStatus('pending');
		$factorModel->setPrice($service['price']);
		$factorModel->setApiId(self::$api->getApiId());
		$factorModel->setBackUri('back uri ....');
		$factorModel->setCreatedIp('ip ...');
		$factorModel->setDueDate(date('Y-m-d H:i:s' , time()+4*24*60*60));
		$factorModel->setCreatedDate(date('Y-m-d H:i:s'));
		if ( isset($userModel) ) {
			if ( $userModel->getUserId() != null )
				$factorModel->setUserId($userModel->getUserId());
			else {
				$s = 0 ;
				// TODO : add user with api
			}
		}
		$factorModel->setModule('module ...');
		$factorModel->setRequestAction('action ...');
		$factorId = $factorModel->insertToDataBase();
		if ( $factorId !== false ){
			/* @var \paymentCms\model\items $itemsModel */
			$itemsModel = self::model('items') ;
			$itemsModel->setPrice($service['price']);
			$itemsModel->setServiceId($service['serviceId']);
			$itemsModel->setDescription($service['description']);
			$itemsModel->setTime(date('Y-m-d H:i:s'));
			$itemsModel->setFactorId($factorModel->getFactorId());
			$itemId = $itemsModel->insertToDataBase();
			if ( $itemId !== false ){
				if ( is_array($data['customField']) and ! empty($data['customField']) ){
					foreach ( $data['customField'] as $fieldId => $fieldValue){
						if ( $fieldValue == null )
							continue ;
						/* @var \paymentCms\model\fieldvalue $fieldValueModel */
						$fieldValueModel = self::model('fieldvalue') ;
						$fieldValueModel->setFactorId($factorModel->getFactorId());
						$fieldValueModel->setFieldId($fieldId);
						$fieldValueModel->setValue($fieldValue);
						$fieldValueStatus = $fieldValueModel->insertToDataBase();
						if ( ! $fieldValueStatus ) {
							$error = rlang('canNotInsertFieldValue');
							break;
						}
					}
				}
			} else {
				$error = rlang('canNotInsertItems');
			}
		} else {
			$error = rlang('canNotInsertFactor');
		}
		if ( $error === false ){
			\database::commit();
			return self::json($factorModel->getFactorId());
		} else {
			return self::jsonError($error,500);
		}
	}
}