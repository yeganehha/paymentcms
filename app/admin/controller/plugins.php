<?php


namespace App\admin\controller;


use mysql_xdevapi\Exception;
use paymentCms\component\cache;
use paymentCms\component\file;
use paymentCms\component\model;
use paymentCms\component\request;
use paymentCms\component\Response;
use paymentCms\component\strings;
use paymentCms\component\validate;
use paymentCms\model\api;

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


class plugins extends \controller {
	public function index(){
		Response::redirect(\app::getBaseAppLink('plugins/lists'));
	}
	public function lists($status = null,$file_name=null) {
		if ( $status == 'appInstalled')
			$this->alert('success' ,null ,rlang('appInstalled'));
		elseif ($status == 'pleaseTryAGain')
			$this->alert('danger' ,null ,rlang('pleaseTryAGain'));
		elseif ($status == 'cantFindInfo')
			$this->alert('danger' ,null ,rlang('cantFindInfo.php'). '<br>' . urldecode($file_name));
		elseif ($status == 'appUninstalled')
			$this->alert('success' ,null ,rlang('appUninstalled'));
		elseif ($status == 'pleaseTryAGain')
			$this->alert('danger' ,null ,rlang('pleaseTryAGain'));
		$apps = \App::appsListWithConfig();
		$this->mold->view('pluginLocalList.mold.html');
		$this->mold->setPageTitle(rlang('plugins'));
		$this->mold->set('activeMenu' , 'plugins');
		$this->mold->set('apps' , $apps);
	}

	public function installLocal($app = null){
		if ( is_null($app)){
			Response::redirect(\app::getBaseAppLink('plugins/lists'));
			return false;
		}
		$appStatus = cache::get('appStatus', $app ,'paymentCms');
		if ( $appStatus == null ){
			$file_name = payment_path.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.$app.DIRECTORY_SEPARATOR.'info.php';
			if ( file_exists($file_name) ) {
				$appData = require_once $file_name;
				if ( isset($appData['db']) and !  is_null($appData['db'])) {
					$query = $this->generateQueryCreatTable($appData['db']);
					model::transaction();
					try {
						if ( model::queryUnprepared($query) ) {
							model::commit();
							$this->changeCacheOfAppStatus($app,'deActive');
							Response::redirect(\app::getBaseAppLink('plugins/lists/appInstalled#app_'.$app));
							return true;
						}
					} catch (Exception $exception){
						model::rollback();
						Response::redirect(\app::getBaseAppLink('plugins/lists/pleaseTryAGain#app_'.$app));
						return true;
					}
				} else {
					$this->changeCacheOfAppStatus($app,'deActive');
					Response::redirect(\app::getBaseAppLink('plugins/lists/appInstalled#app_'.$app));
					return true;
				}
			} else {
				Response::redirect(\app::getBaseAppLink('plugins/lists/cantFindInfo/'.urlencode($file_name).'#app_'.$app));
				return false;
			}
		} else {
			$this->lists();
			return true;
		}
	}

	public function installing($app = null){
		if ( is_null($app)){
			Response::redirect(\app::getBaseAppLink('plugins/lists'));
			return false;
		}
		$appStatus = cache::get('appStatus', $app ,'paymentCms');
		if ( $appStatus == 'deActive' ){
			$this->changeCacheOfAppStatus($app,'active');
			Response::redirect(\app::getBaseAppLink('plugins/lists/appInstalled#app_'.$app));
			return true;
		} else {
			Response::redirect(\app::getBaseAppLink('plugins/lists#app_'.$app));
			return true;
		}
	}
	public function deActive($app = null){
		if ( is_null($app)){
			Response::redirect(\app::getBaseAppLink('plugins/lists'));
			return false;
		}
		$appStatus = cache::get('appStatus', $app ,'paymentCms');
		if ( $appStatus == 'active' ){
			$this->changeCacheOfAppStatus($app,'deActive');
			Response::redirect(\app::getBaseAppLink('plugins/lists/appUninstalled#app_'.$app));
			return true;
		} else {
			Response::redirect(\app::getBaseAppLink('plugins/lists#app_'.$app));
			return true;
		}
	}

	public function uninstallLocal($app = null){
			if ( is_null($app)){
				Response::redirect(\app::getBaseAppLink('plugins/lists'));
				return false;
			}
			$file_name = payment_path.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.$app.DIRECTORY_SEPARATOR.'info.php';
			if ( file_exists($file_name) ) {
				$appData = require_once $file_name;
				if ( isset($appData['db']) and !  is_null($appData['db'])) {
					$query = $this->generateQueryDropTable($appData['db']);
					model::transaction();
					try {
						if ( model::queryUnprepared($query) ) {
							model::commit();
							file::removedir(payment_path.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.$app);
							$this->changeCacheOfAppStatus($app,null);
							Response::redirect(\app::getBaseAppLink('plugins/lists/appUninstalled#app_'.$app));
							return true;
						}
					} catch (Exception $exception){
						model::rollback();
						Response::redirect(\app::getBaseAppLink('plugins/lists/pleaseTryAGain#app_'.$app));
						return true;
					}
				} else {
					file::removedir(payment_path.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.$app);
					$this->changeCacheOfAppStatus($app,null);
					Response::redirect(\app::getBaseAppLink('plugins/lists/appUninstalled#app_'.$app));
					return true;
				}
			} else {
				Response::redirect(\app::getBaseAppLink('plugins/lists/cantFindInfo/'.urlencode($file_name).'#app_'.$app));
				return false;
			}
		}

	public function install(){
		$get = request::post('page=1,perEachPage=25,name' ,null);
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
			//		$d="http://localhost/payment/api/update/version";
			$appsApiLinks ="http://localhost/payment/api/apps/listApps";
			$localData = array(
				'page' => $get['page'] ,
				'perEachPage' => $get['perEachPage'] ,
				'name' => $get['name'] ,
				'version' => PCVERSION ,
				'siteUrl' => \App::getBaseAppLink(null,'core'),
				'lang' => 'fa',
				'theme' => 'default',
				'app' => json_encode(\App::appsListWithConfig()),
			);
			$apps = json_decode(curl($appsApiLinks,$localData),true);
		}
		$pagination = parent::pagination($apps['totalRecords'],$get['page'],$get['perEachPage']);
		$search = $apps['plugins'];
		$this->mold->view('pluginsList.mold.html');
		$this->mold->setPageTitle(rlang('plugins'));
		$this->mold->set('activeMenu' , 'plugins');
		$this->mold->set('apps' , $search);
	}


	private function generateQueryCreatTable($tables){
		$query = '';
		if ( is_array($tables) ) {
			$configDataBase = require_once payment_path. 'core'.DIRECTORY_SEPARATOR. 'config.php';
			foreach ( $tables as $tableName => $tableData) {
				$query .= 'CREATE TABLE IF NOT EXISTS `'.$configDataBase['_dbTableStartWith'].$tableName.'` ('.chr(10) ;
				foreach ( $tableData['fields'] as $fieldName => $fieldData) {
					$query .= '  `'.$fieldName.'` '.$fieldData.','.chr(10) ;
				}
				if ( isset($tableData['PRIMARY KEY']) and is_array($tableData['PRIMARY KEY']) and ! is_null($tableData['PRIMARY KEY'])) {
					foreach ($tableData['PRIMARY KEY'] as $fieldName) {
						$query .= ' PRIMARY KEY (`'.$fieldName.'`) USING BTREE,'.chr(10);
					}
				}
				if ( isset($tableData['KEY']) and is_array($tableData['KEY']) and ! is_null($tableData['KEY'])) {
					foreach ($tableData['KEY'] as $fieldName) {
						$query .= ' KEY `'.$fieldName.'` (`'.$fieldName.'`),'.chr(10);
					}
				}
				if ( isset($tableData['REFERENCES']) and is_array($tableData['REFERENCES']) and ! is_null($tableData['REFERENCES'])) {
					foreach ($tableData['REFERENCES'] as $fieldName => $fieldData) {
						$query .= 'FOREIGN KEY (`'.$fieldName.'`) REFERENCES `'.$fieldData['table'].'`(`'.$fieldData['column'].'`) ON DELETE '.$fieldData['on_delete'].' ON UPDATE '.$fieldData['on_update'].','.chr(10);
					}
				}
				$query = strings::deleteWordLastString($query,','.chr(10) ).chr(10);
				$query .= ') ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;'.chr(10).chr(10);

			}
		}
		return $query ;
	}
	private function generateQueryDropTable($tables){
		$query = '';
		if ( is_array($tables) ) {
			$configDataBase = require_once payment_path. 'core'.DIRECTORY_SEPARATOR. 'config.php';
			foreach ( $tables as $tableName => $tableData) {
				$query .= 'DROP TABLE IF EXISTS `'.$configDataBase['_dbTableStartWith'].$tableName.'` ;'.chr(10) ;
			}
		}
		return $query ;
	}
	private function changeCacheOfAppStatus($app , $status ){
		$appStatus = cache::get('appStatus', null  ,'paymentCms');
		if ( $status == null )
			unset($appStatus[$app]);
		else
			$appStatus[$app] = $status ;
		cache::save($appStatus,'appStatus' , PHP_INT_MAX , 'paymentCms');
	}
}