<?php


namespace App\user\controller;


use paymentCms\component\request;
use paymentCms\component\Response;
use paymentCms\component\session;
use paymentCms\component\validate;

/**
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 4/27/2019
 * Time: 6:00 PM
 * project : paymentCMS
 * virsion : 0.0.0.1
 * update Time : 4/27/2019 - 6:00 PM
 * Discription of this Page :
 */


if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


class access extends \controller  {

	public function login(){
		$this->mold->view('login.mold.html');
		if ( request::isPost()) {
			$get = request::post('password,username' ,null);
			$rules = [
				"password" => ["required", rlang('password')],
				"username" => ["required", rlang('username')],
			];
			$valid = validate::check($get, $rules);
			if ($valid->isFail()){
				$this->alert('danger','',$valid->errorsIn() );
				return false;
			}

			/* @var \App\user\model\user $model */
			$model = $this->model('user' );
			$model->setPassword($get['password']);
			$password = $model->getPassword();

			$model = $this->model('user' , [$password,$get['username'],$get['username']] , 'password = ? and ( phone = ? or email = ?) ' );
			if ( $model->getUserId() == null ){
				$this->alert('danger','',rlang('cantFindUser') );
				return false;
			}
			session::regenerateSessionId();
			session::lifeTime(1 ,'hour')->set('userAppLoginInformation',$model->returnAsArray());
			if ( request::isGet('callBack') ){
				Response::redirect(urldecode(request::getOne('callBack')));
			} else
				Response::redirect(\App::getBaseAppLink(null,'home'));
		}
	}

	public function register(){

	}

	public function logout(){
		session::remove('userAppLoginInformation');
		Response::redirect(\App::getBaseAppLink(null,'home'));
	}
}