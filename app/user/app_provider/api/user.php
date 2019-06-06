<?php

namespace App\user\app_provider\api;

use paymentCms\component\model;
use paymentCms\component\request;
use paymentCms\component\session;
use paymentCms\component\validate;

/**
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 4/19/2019
 * Time: 12:21 AM
 * project : paymentCMS
 * virsion : 0.0.0.1
 * update Time : 4/19/2019 - 12:21 AM
 * Discription of this Page :
 */


if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


class user extends \App\api\controller\innerController  {

	/**
	 * @param $whereValue
	 * @param $whereClause
	 *
	 * @return \paymentCms\model\user|\App\model\model
	 *
	 *                               [no-access]
	 */
	public static function getUser($whereValue , $whereClause) {
		return self::model('user',$whereValue , $whereClause ) ;
	}


	public static function editUser($userId,$data){
		$get = request::getFromArray($data,'fname,lname,email,phone,password,groupId,block=0,admin_note' ,null);
		$rules = [
			"fname" => ["required", rlang('firstName')],
			"lname" => ["required", rlang('lastName')],
			"groupId" => ["required|match:>0", rlang('permission')],
			"email" => ["required|email", rlang('email')],
			"phone" => ["required|mobile", rlang('phone')],
			"block" => ["required|format:{0/1}", rlang('block')],
		];
		if ( $userId == null )
			$rules["password"] = 	 ["required", rlang('password')] ;
		$valid = validate::check($get, $rules);
		if ($valid->isFail()){
			return self::jsonError($valid->errorsIn(),400);
		}
		/* @var \paymentCms\model\user $model */
		if ($userId == null) {
			$model = self::model('user');
		} else {
			$model = self::model('user' , $userId);
			if ( $model->getUserId() != $userId ){
				return self::jsonError(rlang('cantFindUser'),400);
			}
		}
		$model->setUserGroupId($get['groupId']);
		$model->setFname($get['fname']);
		$model->setLname($get['lname']);
		$model->setEmail($get['email']);
		$model->setPhone($get['phone']);
		if ( $get['password'] != null ) $model->setPassword($get['password']);
		$model->setBlock($get['block']);
		$model->setAdminNote($get['admin_note']);
		$model->setRegisterTime( ($model->getRegisterTime() != null ) ? $model->getRegisterTime() : date('Y-m-d H:i:s') );
		if ($userId == null) {
			$result = $model->insertToDataBase();
			if ( $result !== false ) {
				return self::json($model->getUserId());
			} else {
				return self::jsonError(rlang('pleaseTryAGain'),500);
			}
		} else {
			$result = $model->upDateDataBase();
			if ( $result ) {
				if ( $model->getUserId() == session::get('userAppLoginInformation')['userId'])
					session::lifeTime(1 ,'hour')->set('userAppLoginInformation',$model->returnAsArray());
				return self::json($model->getUserId());
			}
			else {
				return self::jsonError(rlang('pleaseTryAGain'),500);
			}
		}
	}


	public static function generateUser($data){
		return self::editUser(null,$data);
	}
}