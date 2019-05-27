<?php


namespace App\core\app_provider\api;


use App\api\controller\service;
use mysql_xdevapi\Exception;
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


class fields extends \App\api\controller\innerController {

	public static function getFieldsToEdit($serviceId , $serviceType , $statusNotBe = null , $defineKeys = false , $fieldsId = null ){
		/* @var \paymentCms\model\field $fieldModel */
		$fieldModel = self::model('field') ;
		$searchWhere = ' serviceType = ? and ( 0 ';
		$searchValue[] = $serviceType ;
		if ( $serviceId != null ){
			$searchWhere .= ' or serviceId IN ('.strings::deleteWordLastString(str_repeat('? , ',count($serviceId)),', ').')' ;
			$searchValue = array_merge($searchValue,(array)$serviceId);
		}
		if ( $fieldsId != null ){
			$searchWhere .= ' or fieldId IN ('.strings::deleteWordLastString(str_repeat('? , ',count($fieldsId)),', ').')' ;
			$searchValue = array_merge($searchValue,(array)$fieldsId);
		}
		$searchWhere .= ' ) ';
		if ( $statusNotBe != null ){
			if ( is_array($statusNotBe) ){
				foreach ($statusNotBe as $statusOne ) {
					$searchValue[] = $statusOne;
					$searchWhere .= ' and status != ? ';
				}
			}
		}
		$fields = $fieldModel->search($searchValue, $searchWhere ,null,'*',['column'=>'orderNumber' , 'type' => 'desc' ]  );
		if ( $fields === true)
			$fields = [] ;

		if ( $defineKeys ) {
			foreach ($fields as $key => $field) {
				if ($field['type'] == 'checkbox' or $field['type'] == 'radio' or $field['type'] == 'select')
					$fields[$key]['valuesDe'] = explode(',', $field['values']);
			}
		}

		return self::json($fields);
	}


	public static function updateFields($serviceId , $serviceType , $fields, $deletedFields = null){
		$form = ['moreField'=>$fields ,'deleteField' =>$deletedFields];
		$rules = [
			'moreField.*.status' => ['format:{visible/invisible/required,admin}'	, rlang('status')],
			'moreField.*.type' => ['format:{text/url/password/email/select/radio/checkbox/textarea/date/number/file}'	, rlang('type')],
			'moreField.*.name' => ['notEmpty'	, rlang('name')],
			'moreField.*.order' => ['number'	, rlang('orderToShow')],
		];
		$valid = validate::check($form, $rules);
		if ($valid->isFail())
			return self::jsonError($valid->errorsIn(),500);
		model::transaction();
		try {
			/* @var \paymentCms\model\field $modelField */
			if (is_array($fields) and count($fields) > 0)
				foreach ($fields as $key => $field) {
					if ($field['id'] > 0)
						$modelField = self::model('field', $field['id']);
					else
						$modelField = self::model('field');
					$modelField->setStatus($field['status']);
					$modelField->setDescription($field['description']);
					$modelField->setOrder($field['order']);
					$modelField->setRegex($field['regex']);
					$modelField->setTitle($field['name']);
					$modelField->setType($field['type']);
					$modelField->setValues($field['value']);
					$modelField->setServiceId($serviceId);
					$modelField->setServiceType($serviceType);
					if ($field['id'] > 0)
						$modelField->upDateDataBase();
					else
						$modelField->insertToDataBase();
				}
			if (is_array($deletedFields) and count($deletedFields) > 0) {
				$modelField->db()->where('fieldId', $deletedFields, 'IN');
				$modelField->db()->where('serviceId', $serviceId);
				$modelField->db()->where('serviceType', $serviceType);
				$modelField->db()->delete('field');
			}
			model::commit();
			return self::json(null);
		} catch (Exception $exception){
			model::rollback();
			return self::jsonError($exception->getMessage(),500);
		}
	}


	public static function fillOutForm($serviceId , $serviceType , $data , $objectId , $objectType ){
		$fields = self::getFieldsToEdit($serviceId,$serviceType , ['admin' , 'invisible'] , true);
		if (!empty($fields) and is_array($fields)) {
			foreach ($fields['result'] as $key => $field) {
				$regix = null;
				if ($field['regex'] != null) {
					$regix = explode(',', $field['regex']);
				}
				if ($field['status'] == 'required') {
					$regix[] = 'required';
				}
				if ($regix == null or count($regix) == 0)
					continue;
				$rules[$field['fieldId']] = [implode('|', array_unique(array_filter($regix))), $field['title']];
			}
		}
		if (isset($rules)) {
			$valid = validate::check($data, $rules);
			if ($valid->isFail()) {
				return self::jsonError($valid->errorsIn());
			}
		}
		if ( is_array($data) and ! empty($data) ){
			model::transaction();
			foreach ( $data as $fieldId => $fieldValue){
				if ( $fieldValue == null )
					continue ;
				/* @var \paymentCms\model\fieldvalue $fieldValueModel */
				$fieldValueModel = self::model('fieldvalue') ;
				$fieldValueModel->setObjectId($objectId);
				$fieldValueModel->setObjectType($objectType);
				$fieldValueModel->setFieldId($fieldId);
				$fieldValueModel->setValue($fieldValue);
				$fieldValueStatus = $fieldValueModel->insertToDataBase();
				if ( ! $fieldValueStatus ) {
					model::rollback();
					return self::jsonError(rlang('canNotInsertFieldValue'),500);
				}
			}
			model::commit();
			return self::json(null);
		}
		return self::json(null);
	}

	public static function showFilledOutForm($serviceId , $serviceType , $objectId , $objectType ,$statusNotBe = null ){
		/* @var \paymentCms\model\fieldvalue $fieldValueModel */
		$fieldValueModel = self::model('fieldvalue') ;
		$fieldsFill = [] ;
		$fieldsFillTemp = $fieldValueModel->search([$objectId,$objectType],'objectId = ? and objectType = ? ' ,'fieldvalue'  );
		if ( is_array($fieldsFillTemp) )
			foreach ( $fieldsFillTemp as $fieldFill) {
				$fieldsFill[ $fieldFill['fieldId'] ] = $fieldFill ;
			}
		unset($fieldsFillTemp);

		$allFields = self::getFieldsToEdit($serviceId,$serviceType , $statusNotBe , true , array_keys($fieldsFill));
		if ( is_array($allFields['result']) )
			foreach ( $allFields['result'] as $index => $allField)
				if ( isset($fieldsFill[$allField['fieldId']]))
					$allFields['result'][$index]['value'] = $fieldsFill[$allField['fieldId']]['value'] ;


		return self::json($allFields['result']);
	}
}