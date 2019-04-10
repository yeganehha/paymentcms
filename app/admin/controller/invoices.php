<?php


namespace App\admin\controller;


use paymentCms\component\model;
use paymentCms\component\request;
use paymentCms\component\Response;
use paymentCms\component\validate;
use paymentCms\model\api;

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
	public function index(){
		Response::redirect(\app::getBaseAppLink('service/lists'));
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
		$this->mold->view('invoiceList.mold.html');
		$this->mold->setPageTitle(rlang('invoices'));
		$this->mold->set('activeMenu' , 'allinvoices');
		$this->mold->set('invoices' , $search);
	}

	public function new(){
		if ( request::isPost() ){
			$this->checkBaseData();
		}
		$this->mold->set('newService',true);
		$this->mold->view('serviceProfile.mold.html');
		$this->mold->setPageTitle(rlang(['add','services']));
	}

	private function checkBaseData(){
		$form = request::post('id=0,name,description,price,link,active');
		$rules = [
			'id' => ['int|match:>=0'	, rlang('id')],
			'name' => ['required'	, rlang('name')],
			'description' => ['required'	, rlang('description')],
			'price' => ['required|int|match:>=0', rlang('price')],
			'link' => ['required'	, rlang('link')],
		];
		$valid = validate::check($form, $rules);
		if ($valid->isFail()){
			$this->alert('warning' , null,$valid->errorsIn(),'error');
			$this->mold->set('post',$form);
		} else {
			/* @var \paymentCms\model\service $model */
			if ( $form['id'] > 0 )
				$model = $this->model('service' , $form['id']) ;
			else
				$model = $this->model('service') ;
			$model->setPrice($form['price']);
			$model->setDescription($form['description']);
			$model->setLink($form['link']);
			$model->setName($form['name']);
			if( $form['active'] == 'active')
				$model->setStatus(true);
			else
				$model->setStatus(false);
			$status = false ;
			if ( $form['id'] == 0 ) {
				$action = 'insert';
				$id = $model->insertToDataBase();
				if ( $id > 0 )
					$status = true ;
			} else {
				$status = $model->upDateDataBase();
				$action = 'update';
			}
			if ($status ) {
				Response::redirect(\app::getBaseAppLink('service/profile/' . $model->getServiceId() . '/'.$action.'ActionDone'));
			} else {
				$this->alert('warning' , null, rlang('pleaseTryAGain'),'error');
				$this->mold->set('post',$form);
			}
		}
	}

	private function checkFieldData(){
		$form = request::post('id,firstNameStatus=visible,lastNameStatus=visible,emailNameStatus=visible,phoneNameStatus=required,moreField,deleteField');
		$rules = [
			'id' => ['int|match:>0'	, rlang('id')],
			'firstNameStatus' => ['format:{visible/invisible/required}'	, rlang(['status','firstName'])],
			'lastNameStatus' => ['format:{visible/invisible/required}'	, rlang(['status','lastName'])],
			'emailStatus' => ['format:{visible/invisible/required}'	, rlang(['status','email'])],
			'phoneStatus' => ['format:{visible/invisible/required}'	, rlang(['status','phone'])],
			'moreField.*.status' => ['format:{visible/invisible/required,admin}'	, rlang('status')],
			'moreField.*.type' => ['format:{text/url/password/email/select/radio/checkbox/textarea/date/number/file}'	, rlang('type')],
			'moreField.*.name' => ['notEmpty'	, rlang('name')],
			'moreField.*.order' => ['number'	, rlang('orderToShow')],
		];
		$valid = validate::check($form, $rules);
		if ($valid->isFail()){
			$this->alert('warning' , null,$valid->errorsIn(),'error');
			$this->mold->set('post',$form);
		} else {
			model::transaction();
			/* @var \paymentCms\model\service $model */
			/* @var \paymentCms\model\field $modelField */
			$model = $this->model('service' , $form['id']) ;
			$model->setFirstNameStatus($form['firstNameStatus']);
			$model->setLastNameStatus($form['lastNameStatus']);
			$model->setEmailStatus($form['emailNameStatus']);
			$model->setPhoneStatus($form['phoneNameStatus']);
			$status = $model->upDateDataBase();
			if ($status ) {
				if ( is_array($form['moreField']) and count($form['moreField']) > 0 )
					foreach ($form['moreField'] as $key => $field ){
					if ( $field['id'] > 0 )
						$modelField = $this->model('field',$field['id']);
					else
						$modelField = $this->model('field');
					$modelField->setStatus($field['status']);
					$modelField->setDescription($field['description']);
					$modelField->setOrder($field['order']);
					$modelField->setRegex($field['regex']);
					$modelField->setTitle($field['name']);
					$modelField->setType($field['type']);
					$modelField->setValues($field['value']);
					$modelField->setServiceId($model->getServiceId());
					if ( $field['id'] > 0 )
						$modelField->upDateDataBase();
					else
						$modelField->insertToDataBase();
				}
				if ( is_array($form['deleteField']) and count($form['deleteField']) > 0 ){
					$modelField->db()->where('fieldId' , $form['deleteField'] , 'IN');
					$modelField->db()->where('serviceId' , $model->getServiceId() );
					$modelField->db()->delete('field' );
				}
				model::commit();
				Response::redirect(\app::getBaseAppLink('service/profile/' . $model->getServiceId() . '/moreConfigurationActionDone'));
			} else {
				model::rollback();
				$this->alert('warning' , null, rlang('pleaseTryAGain'),'error');
				$this->mold->set('post',$form);
			}
		}
	}

	public  function profile($serviceId , $action = null){
		/* @var \paymentCms\model\service $model */
		$model = $this->model('service' , $serviceId) ;
		if ( $model->getServiceId() != $serviceId){
			$this->mold->offAutoCompile();
			\App\core\controller\httpErrorHandler::E404();
			return ;
		}
		if ( request::isPost() ){
			$_POST['id'] = $serviceId ;
			$this->checkBaseData();
			$this->mold->set('activeTab','edit');
		}
		if ( $action == 'updateActionDone' ){
			$this->mold->set('activeTab','edit');
			$this->alert('success',null,rlang(['edit','services','successfully','was']));
		} elseif ( $action == 'insertActionDone' ){
			$this->mold->set('activeTab','dashboard');
			$this->alert('success',null,rlang(['add','services','successfully','was']));
		} elseif ( $action == 'moreConfigurationActionDone' ){
			$this->mold->set('activeTab','moreInfo');
			$this->alert('success',null,rlang(['fields','services','successfully','was']));
		}

		/* @var \paymentCms\model\field $fieldModel */
		$fieldModel = $this->model('field') ;
		$fields = $fieldModel->search($serviceId,'serviceId = ? ' ,null,'*',['column'=>'orderNumber' , 'type' => 'desc' ]  );
		if ( $fields === true)
			$fields = [] ;
		$this->mold->set('service',$model);
		$this->mold->set('fields',$fields);
		$this->mold->set('numberOfFields',count($fields));
		$this->mold->view('serviceProfile.mold.html');
		$this->mold->setPageTitle(rlang(['profile','services']). ': '.$model->getName());
	}

	public function editMore($serviceId){
		/* @var \paymentCms\model\service $model */
		$model = $this->model('service' , $serviceId) ;
		if ( $model->getServiceId() != $serviceId){
			$this->mold->offAutoCompile();
			\App\core\controller\httpErrorHandler::E404();
			return ;
		}
		if ( request::isPost() ){
			$_POST['id'] = $serviceId ;
			$this->checkFieldData();
			$this->mold->set('activeTab','moreInfo');
		} else
			Response::redirect(\app::getBaseAppLink('service/profile/' . $model->getServiceId() ));


		$this->mold->set('activeTab','moreInfo');
		/* @var \paymentCms\model\field $fieldModel */
		$fieldModel = $this->model('field') ;
		$fields = $fieldModel->search($serviceId , 'serviceId = ?' , null ,'*',['column' => 'orderNumber' , 'type' => 'desc']);
		if ( $fields === true)
			$fields = [] ;
		$this->mold->set('service',$model);
		$this->mold->set('fields',$fields);
		$this->mold->set('numberOfFields',count($fields));
		$this->mold->view('serviceProfile.mold.html');
		$this->mold->setPageTitle(rlang(['profile','services']). ': '.$model->getName());
	}
}