<?php


namespace App\user\controller;


use paymentCms\component\request;
use paymentCms\component\Response;
use paymentCms\component\session;

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
			session::regenerateSessionId();
			session::lifeTime(1 ,'hour')->set('userAppLoginInformation',['user_group_id' => 1]);
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