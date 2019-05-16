<?php


namespace App\core\app_provider\api;


use App\api\controller\service;
use mysql_xdevapi\Exception;
use paymentCms\component\model;
use paymentCms\component\request;
use paymentCms\component\security;
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

	public static function getFieldsToEdit($serviceId , $serviceType , $statusNotBe = null , $defineKeys = false ){
		/* @var \paymentCms\model\field $fieldModel */
		$fieldModel = self::model('field') ;
		$searchWhere = 'serviceId = ? and serviceType = ? ';
		$searchValue[] = $serviceId ;
		$searchValue[] = $serviceType ;
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
}