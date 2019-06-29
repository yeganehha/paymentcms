<?php


namespace App\user\app_provider\admin;


use App\core\controller\fieldService;
use App\core\controller\httpErrorHandler;
use App\user\app_provider\api\user;
use paymentCms\component\model;
use paymentCms\component\request;
use paymentCms\component\Response;
use paymentCms\component\session;
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
		$get = request::post('page=1,perEachPage=25,fname,lname,email,phone,userId,customField' ,null);
		$rules = [
			"page" => ["required|match:>0", rlang('page')],
			"perEachPage" => ["required|match:>0|match:<501", rlang('page')],
		];
		$valid = validate::check($get, $rules);
		$value = array( );
		$variable = array( );
		$cfvVariable = [] ;
		if ($valid->isFail()){
			//TODO:: add error is not valid data

		} else {
			if ( $get['customField'] != null and is_array($get['customField'])) {
				foreach ($get['customField'] as $idCustomField => $valueCustomField ){
					if ($valueCustomField != null or $valueCustomField != '') {
						if ( fieldService::saveInTable() ){
							$cfvVariable[] = '  cfv.f_'.$idCustomField.' LIKE "%' . $valueCustomField . '%" ';
						} else {
							$cfvVariable[] = ' ( cfv.fieldId = '.$idCustomField.' and cfv.value LIKE "%' . $valueCustomField . '%" ) ';
						}
					}
				}
			}
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
		if ( count($cfvVariable) > 0 ) {
			if ( fieldService::saveInTable() )
				model::join('customFieldValue_0_user_register cfv', ' ( userId = cfv.objectId and cfv.objectType = "user_register" and (' . implode(' and ', $cfvVariable) . ') )', "INNER");
			else
				model::join('fieldvalue cfv', ' ( userId = cfv.objectId and cfv.objectType = "user_register" and (' . implode(' or ', $cfvVariable) . ') )', "INNER");
		}
		$numberOfAll = ($model->search( (array) $value  , ( count($variable) == 0 ) ? null : implode(' or ' , $variable) , null, 'COUNT(userId) as co' )) [0]['co'];
		$pagination = parent::pagination($numberOfAll,$get['page'],$get['perEachPage']);
		if ( count($cfvVariable) > 0 ) {
			if ( fieldService::saveInTable() )
				model::join('customFieldValue_0_user_register cfv', ' ( userId = cfv.objectId and cfv.objectType = "user_register" and (' . implode(' and ', $cfvVariable) . ') )', "INNER");
			else
				model::join('fieldvalue cfv', ' ( userId = cfv.objectId and cfv.objectType = "user_register" and (' . implode(' or ', $cfvVariable) . ') )', "INNER");
		}
		$search = $model->search( (array) $value  , ( ( count($variable) == 0 ) ? null : implode('or' , $variable) )  , null, '*'  , ['column' => 'userId' , 'type' =>'desc'] , [$pagination['start'] , $pagination['limit'] ] );
		$fields = fieldService::getFieldsToFillOut(0,'user_register' );
		$this->mold->set('fields' , $fields['result']);
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
		fieldService::getFieldsToFillOut(0,'user_register' ,$this->mold);
		$this->mold->set('newUser',true);
		$this->mold->path('default', 'user');
		$this->mold->view('userProfile.mold.html');
		$this->mold->setPageTitle(rlang(['add','user']));
	}
	public function profile($userId,$updateStatus = null){
		if ( request::isPost() ) {
			$this->checkData($userId);
		}
		/* @var \paymentCms\model\user $user */
		$user = $this->model('user' , $userId );
		if ( $user->getUserId() != $userId ){
			httpErrorHandler::E404();
			return false ;
		}
		if ( $updateStatus == 'updateDone') {
			$this->alert('success' , '',rlang('editUserSuccessFully'));
//			$this->mold->set('activeTab','edit');
		}elseif ( $updateStatus == 'insertDone') {
			$this->alert('success' , '',rlang('insertUserSuccessFully'));
		}

		/* @var \App\user\model\user_group $model */
		$model = $this->model('user_group');
		$access = $model->search(null,null);

		fieldService::showFilledOutFormWithAllFields(0,'user_register',$user->getUserId() , 'user_register' , true,$this->mold);
		$this->mold->set('access',$access);
		$this->mold->set('user',$user);
		$this->mold->path('default', 'user');
		$this->mold->view('userProfile.mold.html');
		$this->mold->setPageTitle(rlang(['profile','user']));
		return $user;
	}

	/**
	 * @param null $updateStatus
	 * [user-access]
	 * @return bool|\paymentCms\model\user
	 */
	public function myProfile($updateStatus = null){
		$userLogin = user::getUserLogin();
		$this->mold->set('myProfile' , true);
		$myProfile = true ;
		$_POST['groupId'] = $userLogin['user_group_id'];
		$userId = $userLogin['userId'];

		if ( request::isPost() ) {
			$this->checkData($userId , true);
		}
		/* @var \paymentCms\model\user $user */
		$user = $this->model('user' , $userId );
		if ( $user->getUserId() != $userId ){
			httpErrorHandler::E404();
			return false ;
		}
		if ( $updateStatus == 'updateDone') {
			$this->alert('success' , '',rlang('editUserSuccessFully'));
//			$this->mold->set('activeTab','edit');
		}elseif ( $updateStatus == 'insertDone') {
			$this->alert('success' , '',rlang('insertUserSuccessFully'));
		}

		/* @var \App\user\model\user_group $model */
		$model = $this->model('user_group');
		$access = $model->search(null,null);

		$this->mold->set('access',$access);
		$this->mold->set('user',$user);
		$this->mold->path('default', 'user');
		$this->mold->view('userProfile.mold.html');
		$this->mold->setPageTitle(rlang(['profile','user']));
		return $user;
	}

	/**
	 * @param null $userId
	 *
	 * @return bool
	 * [no-access]
	 */
	private function checkData($userId = null,$myPerofile = false){
		$result = \App\user\app_provider\api\user::editUser($userId,$_POST);
		if ( $result['status'] ){
			if ( $myPerofile )
				$link = 'myProfile' ;
			else
				$link = 'profile/'.$result['result'] ;
			if ($userId == null) {
				Response::redirect(\App::getBaseAppLink('users/' . $link . '/insertDone', 'admin'));
			} else {
				Response::redirect(\App::getBaseAppLink('users/' . $link . '/updateDone', 'admin'));
			}
			exit;
		} else {
			$this->alert('danger', '', $result['massage'] );
			$this->mold->set('activeTab','edit');
			return false;
		}
	}



	public function insertForm(){
		if ( request::isPost() ) {
			$form = request::post('moreField,deleteField');
			$resultUpdateField = fieldService::updateFields(0,'user_register' ,$form['moreField'],$form['deleteField']);
			if ( ! $resultUpdateField['status'] ) {
				$this->alert('warning' , null, rlang('pleaseTryAGain').'<br>'.$resultUpdateField['massage'],'error');
			} else {
				$this->alert('success' , null, rlang('eFormRegisterSuccess'));
			}
		}
//		/* @var \App\user\model\user_group $model */
//		$model = $this->model(['user','user_group']);
//		$access = $model->search(null,null);
//		$this->mold->set('access',$access);
		fieldService::getFieldsToEdit(0,'user_register',$this->mold);
		$this->mold->path('default', 'user');
		$this->mold->view('formEditor.mold.html');
		$this->mold->path('default');
		$this->mold->setPageTitle(rlang('eFormRegister'));
	}
}