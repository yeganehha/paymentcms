<?php


namespace App\user\app_provider\admin;


use paymentCms\component\model;
use paymentCms\component\request;
use paymentCms\component\Response;
use paymentCms\component\strings;
use paymentCms\component\validate;
use paymentCms\model\api;
use paymentCms\model\invoice;

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


class users extends \controller {
	public function index(){
		$this->lists();
	}
	public function lists() {
		$get = request::post('page=1,perEachPage=25,fname,lname,email,phone,userId' ,null);
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
			if ( $get['fname'] != null ) {
				$value[] = '%'.$get['fname'].'%' ;
				$variable[] = 'fname LIKE ?' ;
			}
			if ( $get['lname'] != null ) {
				$value[] = '%'.$get['lname'].'%' ;
				$variable[] = 'lname LIKE ?' ;
			}
			if ( $get['email'] != null ) {
				$value[] = '%'.$get['email'].'%' ;
				$variable[] = 'email LIKE ?' ;
			}
			if ( $get['phone'] != null ) {
				$value[] = '%'.$get['phone'].'%' ;
				$variable[] = 'phone LIKE ?' ;
			}
			if ( $get['userId'] != null ) {
				$value[] = '%'.$get['userId'].'%' ;
				$variable[] = 'userId LIKE ?' ;
			}
		}
		$model = parent::model('user');
		$numberOfAll = ($model->search( (array) $value  , ( count($variable) == 0 ) ? null : implode(' or ' , $variable) , null, 'COUNT(userId) as co' )) [0]['co'];
		$pagination = parent::pagination($numberOfAll,$get['page'],$get['perEachPage']);
		$search = $model->search( (array) $value  , ( ( count($variable) == 0 ) ? null : implode('or' , $variable) )  , null, '*'  , ['column' => 'userId' , 'type' =>'desc'] , [$pagination['start'] , $pagination['limit'] ] );
		$this->mold->path('default', 'user');
		$this->mold->view('userList.mold.html');
		$this->mold->setPageTitle(rlang('users'));
		$this->mold->set('activeMenu' , 'users');
		$this->mold->set('users' , $search);
	}
	public function insert(){
		if ( request::isPost() ) {
			$this->checkData();
		}
		/* @var \App\user\model\user_group $model */
		$model = $this->model('user_group');
		$access = $model->search(null,null);
		$this->mold->set('access',$access);
		$this->mold->set('newUser',true);
		$this->mold->path('default', 'user');
		$this->mold->view('userProfile.mold.html');
		$this->mold->setPageTitle(rlang(['add','user']));
	}
	private function checkData($userId = null){
		$get = request::post('fname,lname,email,phone,password,groupId,block=0,admin_note' ,null);
		$rules = [
			"fname" => ["required", rlang('firstName')],
			"lname" => ["required", rlang('lastName')],
			"groupId" => ["required|match:>0", rlang('permission')],
			"password" => ["required", rlang('password')],
			"email" => ["required|email", rlang('email')],
			"phone" => ["required|mobile", rlang('phone')],
			"block" => ["required|format:{0/1}", rlang('block')],
		];
		$valid = validate::check($get, $rules);
		if ($valid->isFail()){
			$this->alert('danger','',$valid->errorsIn() );
			return false;
		}
		/* @var \App\user\model\user $model */
		if ($userId == null) {
			$model = $this->model('user');
		} else {
			$model = $this->model('user' , $userId);
			if ( $model->getUserId() != $userId ){
				$this->alert('danger','',rlang('cantFindUser') );
				return false;
			}
		}
		$model->setUserGroupId($get['groupId']);
		$model->setFname($get['fname']);
		$model->setLname($get['lname']);
		$model->setEmail($get['email']);
		$model->setPhone($get['phone']);
		$model->setPassword($get['password']);
		$model->setBlock($get['block']);
		$model->setAdminNote($get['admin_note']);
		$model->setRegisterTime( ($model->getRegisterTime() != null ) ? $model->getRegisterTime() : date('Y-m-d H:i:s') );
		if ($userId == null) {
			$result = $model->insertToDataBase();
			if ( $result !== false ) {
				Response::redirect(\App::getBaseAppLink('users/profile/' . $result, 'admin'));
				exit;
			} else {
				$this->alert('danger','',rlang('tryAGain') );
				return false;
			}
		} else {
			$result = $model->upDateDataBase();
			if ( $result )
				Response::redirect(\App::getBaseAppLink('users/profile/'.$result.'/updateDone' , 'admin'));
			else
				Response::redirect(\App::getBaseAppLink('users/profile/'.$result.'/updateError' , 'admin'));
			exit;
		}
	}
}