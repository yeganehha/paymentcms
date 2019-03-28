<?php


namespace App\admin\controller;


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


class service extends \controller {
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
		$model = parent::model('service');
		$numberOfAll = ($model->search( (array) $value  , ( count($variable) == 0 ) ? " 1 " : implode('or' , $variable) , 'service', 'COUNT(serviceId) as co' )) [0]['co'];
		$pagination = parent::pagination($numberOfAll,$get['page'],$get['perEachPage']);
		$search = $model->search( (array) $value  , ( ( count($variable) == 0 ) ? " 1 " : implode('or' , $variable) ) . 'order by serviceId DESC limit '.$pagination['start'].','.$pagination['limit'] , 'service', '*' );
		$this->mold->view('listService.mold.html');
		$this->mold->setPageTitle(rlang('services'));
		$this->mold->set('activeMenu' , 'services');
		$this->mold->set('services' , $search);
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
				$model->setStatus(1);
			else
				$model->setStatus(0);
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
		$form = request::post('id,firstNameStatus=visible,lastNameStatus=visible,emailNameStatus=visible,phoneNameStatus=required');
		$rules = [
			'id' => ['int|match:>0'	, rlang('id')],
			'firstNameStatus' => ['format:{visible/invisible/required}'	, rlang(['status','firstName'])],
			'lastNameStatus' => ['format:{visible/invisible/required}'	, rlang(['status','lastName'])],
			'emailStatus' => ['format:{visible/invisible/required}'	, rlang(['status','email'])],
			'phoneStatus' => ['format:{visible/invisible/required}'	, rlang(['status','phone'])],
		];
		$valid = validate::check($form, $rules);
		if ($valid->isFail()){
			$this->alert('warning' , null,$valid->errorsIn(),'error');
			$this->mold->set('post',$form);
		} else {
			/* @var \paymentCms\model\service $model */
			$model = $this->model('service' , $form['id']) ;
			$model->setFirstNameStatus($form['firstNameStatus']);
			$model->setLastNameStatus($form['lastNameStatus']);
			$model->setEmailStatus($form['emailStatus']);
			$model->setPhoneStatus($form['phoneStatus']);
			$status = $model->upDateDataBase();
			if ($status ) {
				Response::redirect(\app::getBaseAppLink('service/profile/' . $model->getServiceId() . '/moreConfigurationActionDone'));
			} else {
				$this->alert('warning' , null, rlang('pleaseTryAGain'),'error');
				$this->mold->set('post',$form);
			}
		}
	}
}