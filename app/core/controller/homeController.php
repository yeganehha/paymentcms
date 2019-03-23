<?php
/**
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 3/24/2019
 * Time: 7:58 PM
 * project : paymentCMS
 * version : 0.0.0.1
 * update Time : 3/24/2019 - 7:58 PM
 * Description of this Page :
 */


namespace controller;


if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


class home extends \controller {

	public function __construct() {

	}

	public function index($params = null){
		//parent::view('home',$params);
		$model = parent::model('contact');
		$listContact = $model->search(array() , ' 1 ' );
		parent::view('home',array('listContent' => $listContact ));
	}

	public function search($params = null){
		$dataGet = \request::get(array('search'));
		$validate = new \validate($dataGet,array(
			'search' => 'required|notEmpty'
		));
		$model = parent::model('contact');
		if ( $validate->isValid() ) {
			$validData = $validate->getReturnData() ;
			$listContact = $model->search(array('%'.$validData['search'].'%','%'.$validData['search'].'%','%'.$validData['search'].'%','%'.$validData['search'].'%'), ' lastName LIKE ? or  firstName LIKE ? or  phone LIKE ? or  email LIKE  ? ');
		} else {
			$listContact = $model->search(array() , ' 1 ' );
		}
		parent::view('home',array('listContent' => $listContact ));
	}

	public function edit(){
		$dataGet = \request::post(array('id' => '0', 'firstName' , 'lastName' , 'phone' , 'email'));
		$validate = new \validate($dataGet,array(
			'id' => 'required|notEmpty|number',
			'firstName' => 'required|notEmpty',
			'lastName' => 'required|notEmpty',
			'phone' => 'required|notEmpty|numberFormat:{+98/0/}{91/90/92/93/4X/8X/3X/7X/2X/5X/6X/1X}XXXXXXXX',
			'email' => 'email'
		));
		if ( $validate->isValid() ){
			$validData = $validate->getReturnData() ;
			if ( $validData['id'] > 0 )
				$model = parent::model('contact' ,$validData['id']);
			else
				$model = parent::model('contact');
			$model->setEmail($validData['email']);
			$model->setLastName($validData['lastName']);
			$model->setPhone($validData['phone']);
			$model->setFirstName($validData['firstName']);
			if ( $model->getId() > 0 ){
				$resultEdit = $model->upDateDataBase();
				if ( $resultEdit ) {
					header('Location: '.HTTP_ROOT.'home/index/editDone/'. $model->getId());
				}
			} else {
				$idOfInsertToDB = $model->insertToDataBase();
				if ($idOfInsertToDB > 0) {
					header('Location: ' . HTTP_ROOT . 'home/index/insertDone/' . $idOfInsertToDB);
				}
			}
		} else {
			show($validate->getError());
		}
	}

	public function delete(){
		$dataGet = \request::post(array('deleted'));
		$validate = new \validate($dataGet,array(
			'deleted.*' => 'required|notEmpty|number'
		));
		if ( $validate->isValid() ) {
			$validData = $validate->getReturnData();
			foreach ($validData['deleted'] as $key => $id) {
				$model = parent::model('contact', $id);
				$result = $model->deleteFromDataBase();
			}
		}
		header('Location: '.HTTP_ROOT.'home/index/deleteDone');
	}
}