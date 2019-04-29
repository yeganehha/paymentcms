<?php


namespace App\user;

use App\core\controller\httpErrorHandler;
use paymentCms\component\cache;
use paymentCms\component\model;
use paymentCms\component\Response;
use paymentCms\component\session;
use pluginController;

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
		$this->menu->after('dashboard','users' , rlang(['list','users'] ) , \app::getBaseAppLink('users/lists') , 'fa fa-users' );
		$this->menu->addChild('configuration' ,'permission', rlang('permission' ) , \app::getBaseAppLink('permissions','admin') , 'fa fa-lock' );
		$this->mold->path('default','user');
		$this->mold->view('adminHeaderNavItem.header.mold.html');
	}


	public function _controllerStartToRun(){
		$app = \App::getApp();
		$appProvider = \App::getAppProvider();
		$controller = \App::getController();
		$method = \App::getMethod();

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
			model::join('user_group' , 'user_group.user_groupId = user_group_permission.user_groupId');
			if ( $groupId == null )
				return model::searching($page, 'user_group_permission.accessPage = ? and user_group.loginRequired = 0 ', 'user_group_permission' , 'user_group_permission.user_groupId,accessPage,loginRequired');
			else
				return model::searching([$page,$groupId], 'user_group_permission.accessPage = ? and user_group_permission.user_groupId = ? ' , 'user_group_permission' , 'user_group_permission.user_groupId,accessPage,loginRequired');

		} else {
			$permission = cache::get('userPermissions',null , 'user');
			$result = null ;
			for ( $i = 0 ; $i < count($permission) ; $i ++ ){
				if ( $groupId != null and $permission[$i]['user_groupId'] == $groupId and $permission[$i]['accessPage']  == $page )
						return $permission[$i];
				elseif ( $groupId == null and $permission[$i]['accessPage']  == $page ) {
					$result[$permission[$i]['loginRequired']] = $permission[$i];
				}
			}
			if ( isset($result[0]))
				return $result[0];
			elseif ( isset($result[1]))
				return $result[1];
			return null;
		}
	}
}