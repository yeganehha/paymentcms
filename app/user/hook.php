<?php


namespace App\user;

use App\core\controller\httpErrorHandler;
use paymentCms\component\cache;
use paymentCms\component\model;
use paymentCms\component\Response;
use paymentCms\component\session;
use paymentCms\component\strings;
use pluginController;
use ReflectionClass;

/**
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 4/16/2019
 * Time: 11:16 AM
 * project : paymentCMS
 * virsion : 0.0.0.1
 * update Time : 4/16/2019 - 11:16 AM
 * Discription of this Page :
 */




if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


class hook extends pluginController {

	public function _adminHeaderNavbar($vars2){
		$this->menu->after('dashboard','users' , rlang(['list','users'] ) , \app::getBaseAppLink('users/lists','admin') , 'fa fa-users' );
		$this->menu->addChild('configuration' ,'permission', rlang('permission' ) , \app::getBaseAppLink('permissions','admin','admin') , 'fa fa-lock' );
		$this->mold->path('default','user');
		$this->mold->view('adminHeaderNavItem.header.mold.html');
	}


	public function _controllerStartToRun(){
		$app = \App::getApp();
		$appProvider = \App::getAppProvider();
		$controller = \App::getController();
		$method = \App::getMethod();

		if ( $appProvider == null )
			$className ='App\\'.$app.'\controller\\'.$controller ;
		else
			$className ='App\\'.$appProvider.'\app_provider\\'.$app.'\\'.$controller ;
		if ( ( $resultCommentCheck = $this->checkCommentAccess($className,$method) ) !== null ) {
			if ( ! $resultCommentCheck )
				httpErrorHandler::E403();
			return $resultCommentCheck;
		}

		if ( $app == 'user' and $controller == 'access' )
			return true;

		if ( session::has('userAppLoginInformation') ) {
			$user = session::get('userAppLoginInformation');
			$userPermission = self::getPermissionOfGroupId($app.'_'.$controller.'_'.$method , $user['user_group_id']);
			if ($userPermission == null) {
				httpErrorHandler::E403();
				return false ;
			}
			return true;
		} else {
			$userPermission = self::getPermissionOfGroupId($app . '_' . $controller . '_' . $method);
			if ($userPermission == null) {
				httpErrorHandler::E403();
				return false ;
			} elseif ( $userPermission['loginRequired'] ){
				$link = \App::getFullRequestUrl();
				Response::redirect(\App::getBaseAppLink('access/login?callBack='.urlencode($link) , 'user') );
				return false ;
			}
			return true;
		}
	}


	private function getPermissionOfGroupId($page , $groupId = null){
		if ( ! cache::hasLifeTime('userPermissions' , 'user')) {
			$this->savePermissionOfGroupId();
		}
		$permission = cache::get('userPermissions',null , 'user');
		$result = null ;
		for ( $i = 0 ; $i < count($permission) ; $i ++ ){
			if ( $groupId != null and $permission[$i]['user_groupId'] == $groupId and ( $permission[$i]['accessPage']  == $page or $permission[$i]['accessPage']  == '--FULL-ACCESS--') )
					return $permission[$i];
			elseif ( $groupId == null and ( $permission[$i]['accessPage']  == $page or $permission[$i]['accessPage']  == '--FULL-ACCESS--') ) {
				$result[$permission[$i]['loginRequired']] = $permission[$i];
			}
		}
		if ( isset($result[0]))
			return $result[0];
		elseif ( isset($result[1]))
			return $result[1];
		return null;
	}
	private static function checkCommentAccess($controllerClass, $method )
	{
		if (version_compare(phpversion(), '5.0.0', '<')) {
			return null;
		}
		try {
			$rc = new ReflectionClass($controllerClass);
			$stringClass = $rc->getDocComment();
			if ( ( $tempReturn = self::checkAccessComment($stringClass)) === null ){
				$stringMethod = $rc->getMethod($method)->getDocComment();
				return self::checkAccessComment($stringMethod);
			}
			return $tempReturn ;
		} catch (\ReflectionException $e) {
			return null;
		}
	}

	private static function checkAccessComment($string)
	{
		$pattern = "[no-access]";
		if (strings::strhas($string, $pattern)) {
			return false;
		}

		$pattern = "[user-access]";
		if (strings::strhas($string, $pattern) ) {
			if ( ! session::has('userAppLoginInformation') )
				return false;
		}

		$pattern = "[notUser-access]";
		if (strings::strhas($string, $pattern) ) {
			if ( session::has('userAppLoginInformation') )
				return false;
			return true;
		}

		$pattern = "[global-access]";
		if (strings::strhas($string, $pattern) ) {
			return true;
		}

		return null ;
	}
	private function savePermissionOfGroupId(){
		model::join('user_group as user_group' , 'user_group.user_groupId = user_group_permission.user_groupId');
		$permission = model::searching(null, null, 'user_group_permission as user_group_permission' , 'user_group_permission.user_groupId,accessPage,loginRequired');
		return cache::save($permission, 'userPermissions', PHP_INT_MAX , 'user');
	}
}