<?php


namespace App\landing\app_provider\admin;


use App\core\controller\httpErrorHandler;
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


class landingPage extends \controller {
	public function index(){
		$this->lists();
	}
	public function lists() {
		$get = request::post('page=1,perEachPage=25,name,default' ,null);
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
				$value[] = '%'.$get['fname'].'%' ;
				$value[] = '%'.$get['fname'].'%' ;
				$value[] = '%'.$get['fname'].'%' ;
				$variable[] = 'name LIKE ? or metaDescription LIKE ? or template LIKE ?' ;
			}

		}
		$model = parent::model('landingpage');
		$numberOfAll = ($model->search( (array) $value  , ( count($variable) == 0 ) ? null : implode(' or ' , $variable) , null, 'COUNT(landingPageId) as co' )) [0]['co'];
		$pagination = parent::pagination($numberOfAll,$get['page'],$get['perEachPage']);
		$search = $model->search( (array) $value  , ( ( count($variable) == 0 ) ? null : implode('or' , $variable) )  , null, '*'  , ['column' => 'landingPageId' , 'type' =>'desc'] , [$pagination['start'] , $pagination['limit'] ] );
		$this->mold->path('default', 'landing');
		$this->mold->view('landingPageList.mold.html');
		$this->mold->setPageTitle(rlang('pages'));
		$this->mold->set('activeMenu' , 'landingPages');
		$this->mold->set('pages' , $search);
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
			$this->mold->set('activeTab','edit');
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
	public function checkData($userId = null){
		$result = \App\user\app_provider\api\user::editUser($userId,$_POST);
		if ( $result['status'] ){
			if ($userId == null) {
				Response::redirect(\App::getBaseAppLink('users/profile/' . $result['result'] . '/insertDone', 'admin'));
			} else {
				Response::redirect(\App::getBaseAppLink('users/profile/' . $result['result'] . '/updateDone', 'admin'));
			}
			exit;
		} else {
			$this->alert('danger', '', $result['massage'] );
			$this->mold->set('activeTab','edit');
			return false;
		}
	}
}