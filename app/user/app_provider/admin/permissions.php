<?php


namespace App\user\app_provider\admin;


use paymentCms\component\cache;
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


class permissions extends \controller {

	public function index(){
		$this->lists();
	}

	public function lists() {
		$get = request::post('page=1,perEachPage=25,name,gustUser' ,null);
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
			if ( $get['gustUser'] == 'active' ) {
				$value[] = '0' ;
				$variable[] = 'loginRequired = ?' ;
			}
		}
		/* @var \App\user\model\user_group $model */
		$model = parent::model('user_group');
		$numberOfAll = ($model->search( (array) $value  , ( count($variable) == 0 ) ? null : implode('or' , $variable) , null, 'COUNT(user_groupId) as co' )) [0]['co'];
		$pagination = parent::pagination($numberOfAll,$get['page'],$get['perEachPage']);
		$search = $model->search( (array) $value  , ( ( count($variable) == 0 ) ? null : implode('or' , $variable) )  , null, '*'  , ['column' => 'user_groupId' , 'type' =>'desc'] , [$pagination['start'] , $pagination['limit'] ] );
		$this->mold->path('default', 'user');
		$this->mold->view('user_group.mold.html');
		$this->mold->set('activeMenu' , 'permission');
		$this->mold->setPageTitle(rlang('permission'));
		$this->mold->set('groups' , $search);
	}

	public function insert(){
		if ( request::isPost() )
			$this->checkData(0);

		$permission = \App::appsControllerMethodList();
		$this->mold->path('default', 'user');
		$this->mold->set('permissions', $permission);
		$this->mold->set('edit', false);
		$this->mold->view('user_group_permission.mold.html');
		$this->mold->setPageTitle(rlang(['add','permission']));
	}

	public function edit($groupId){
		if ( request::isPost() )
			$this->checkData($groupId);
		else {
			/* @var \App\user\model\user_group $model */
			$model = parent::model('user_group', $groupId);
			if ($model->getUserGroupId() != $groupId) {
				Response::redirect(\App::getBaseAppLink('permissions', 'admin'));
				return false;
			}
			$this->mold->set('permission', $model);
			$accessPages = $this->getPermissionOfGroupId($groupId);
			$accessPage = [];
			for ( $i = 0 ; $i < count($accessPages) ; $i++ ){
				$accessPage[] = $accessPages[$i]['accessPage'];
			}
			$this->mold->set('permissionActive',$accessPage);
		}

		$permission = \App::appsControllerMethodList();
		$this->mold->path('default', 'user');
		$this->mold->set('permissions', $permission);
		$this->mold->set('edit', true);
		$this->mold->set('editId', $groupId);
		$this->mold->view('user_group_permission.mold.html');
		$this->mold->setPageTitle(rlang(['edit','permission']));
	}

	private function checkData($user_groupId = 0 ){
		$get = request::post('name,loginRequired,permission' ,null);
		$rules = [
			"name" => ["required", rlang('name')],
		];
		$valid = validate::check($get, $rules);
		if ($valid->isFail()){
			parent::alert('danger',null,$valid->errorsIn());
			return false ;
		} 
		
		$error = false ;
		model::transaction();
		/* @var \App\user\model\user_group $model */
		if ( $user_groupId == 0 )
			$model = parent::model('user_group' );
		else {
			$model = parent::model('user_group', $user_groupId);
			if ( $model->getUserGroupId() != $user_groupId ){
				model::rollback();
				Response::redirect(\App::getBaseAppLink('permissions','admin'));
				return false ;
			}
		}
		$model->setName($get['name']);
		if ( $get['loginRequired'] == 'active' ) {
			$model->setLoginRequired(1);
		} else
			$model->setLoginRequired(0);
		
		if (  $user_groupId == 0  ){
			$result = $model->insertToDataBase();
			if ( $result === false ){
				$error = true ;
			}
		} else {
			$result = $model->upDateDataBase();
			if ( $result === false ){
				$error = true ;
			}
		}
		
		if ( ! $error ) {
			/* @var \App\user\model\user_group_permission $permissionModel */
			$permissionModel = parent::model('user_group_permission');
			$permissionModel->setUserGroupId($model->getUserGroupId());
			if (  $user_groupId != 0  )
				$permissionModel->deleteFromDataBase();
			for ($i = 0; $i < count($get['permission']); $i++) {
				$permissionModel->setAccessPage($get['permission'][$i]);
				$result = $permissionModel->insertToDataBase();
				if ($result === false) {
					$error = true;
					break;
				}
			}
		}
		if ( $error ){
			$this->alert('danger','',rlang('pleaseTryAGain'));
			model::rollback();
			return false ;
		} else {
			model::commit();
			$this->savePermissionOfGroupId();
			if (  $user_groupId == 0  )
				Response::redirect(\App::getBaseAppLink('permissions/insertDone','admin'));
			else
				Response::redirect(\App::getBaseAppLink('permissions/editDone','admin'));
			return true ;
		}
		
	}


	private function getPermissionOfGroupId($groupId = null){
		if ( ! cache::hasLifeTime('userPermissions' , 'user')) {
			model::join('user_group' , 'user_group.user_groupId = user_group_permission.user_groupId');
			if ( $groupId == null )
				return model::searching(null, null, 'user_group_permission' , 'user_group_permission.user_groupId,accessPage,loginRequired');
			else
				return model::searching($groupId, 'user_group_permission.user_groupId = ? ' , 'user_group_permission' , 'user_group_permission.user_groupId,accessPage,loginRequired');

		} else {
			$permission = cache::get('userPermissions',null , 'user');
			if ( $groupId != null ){
				$result = [] ;
				for ( $i = 0 ; $i < count($permission) ; $i ++ ){
					if ( $permission[$i]['user_groupId'] == $groupId)
						$result[] = $permission[$i];
				}
				return $result;
			}
			else
				return $permission;
		}
	}

	private function savePermissionOfGroupId(){
		model::join('user_group' , 'user_group.user_groupId = user_group_permission.user_groupId');
		$permission = model::searching(null, null, 'user_group_permission' , 'user_group_permission.user_groupId,accessPage,loginRequired');
		return cache::save($permission, 'userPermissions', PHP_INT_MAX , 'user');
	}
}