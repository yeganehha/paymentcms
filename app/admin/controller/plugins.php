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
	public function pLists($status = null,$file_name=null){
		$this->mold->set('pluginsActive' , true);
		$this->lists($status ,$file_name);
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
		elseif ($status == 'appDownloadSuccessfully')
			$this->alert('success' ,null ,rlang('appDownloadSuccessfully'));
		$apps = \App::appsListWithConfig();
		$plugins = \App::pluginsListWithConfig();
		$this->mold->view('pluginLocalList.mold.html');
		$this->mold->setPageTitle(rlang('plugins'));
		$this->mold->set('activeMenu' , 'plugins');
		$this->mold->set('apps' , $apps);
		$this->mold->set('plugins' , $plugins);
	}


	public function installFromStorage(){
		$form = request::post('app,link,type=app,installType=none');
		if ( $form['installType'] == 'install' or $form['installType'] == 'update'  ){
			if ( $form['type'] == 'app')
				$destinationFolder = payment_path.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR ;
			else
				$destinationFolder = payment_path.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR ;
			if ( is_dir($destinationFolder.$form['app']) and  $form['installType'] == 'install' ) {
				$appStatus = cache::get('appStatus', $form['app']  ,'paymentCms');
				if ( $appStatus == null and  $form['type'] == 'app' ){
					Response::redirect(\app::getBaseAppLink('plugins/installLocal/'.$form['app']));
					return true ;
				} elseif( $appStatus == null and  $form['type'] == 'app' ){
					Response::redirect(\app::getBaseAppLink('plugins/installingPlugin/'.$form['app']));
					return true ;
				}
			}
			$folder = payment_path.'core'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'storage' .DIRECTORY_SEPARATOR ;
			file::make_folder($folder);
			$tempName = $form['app'].'_'.md5($form['app'].time().$form['type']).'.zip';
			if ( copy($form['link'],$folder.$tempName ) ) {
				if ( file::unzip($folder.$tempName,$destinationFolder) ){
					file::remove_file($folder.$tempName);
					Response::redirect(\app::getBaseAppLink('plugins/lists/appDownloadSuccessfully'));
					return true ;
				} else {
					file::remove_file($folder.$tempName);
					Response::redirect(\app::getBaseAppLink('plugins/install/extractError'));
					return false ;
				}
			} else {
				Response::redirect(\app::getBaseAppLink('plugins/install/downloadError'));
				return false ;
			}
		} else {
			Response::redirect(\app::getBaseAppLink('plugins/lists'));
			return false ;
		}
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
					$querys = $this->generateQueryCreatTable($appData['db']);
					model::transaction();
					try {
						$hasError = false ;
						foreach ( $querys as $tabelName => $query){
							if ( model::queryUnprepared($query) === false) {
								$hasError = true ;
							}
						}
						if ( $hasError === false ){
							model::commit();
							$this->changeCacheOfAppStatus($app,'deActive');
							Response::redirect(\app::getBaseAppLink('plugins/lists/appInstalled#app_'.$app));
							return true;
						} else {
							model::rollback();
							Response::redirect(\app::getBaseAppLink('plugins/lists/pleaseTryAGain#app_'.$app));
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

	public function installingPlugin($plugin = null){
		if ( is_null($plugin)){
			Response::redirect(\app::getBaseAppLink('plugins/pLists'));
			return false;
		}
		$appStatus = cache::get('pluginStatus', $plugin ,'paymentCms');
		if ( $appStatus == 'deActive' or   $appStatus == null ){
			$this->changeCacheOfAppStatus($plugin,'active' , 'pluginStatus');
			Response::redirect(\app::getBaseAppLink('plugins/pLists/appInstalled#app_'.$plugin));
			return true;
		} else {
			Response::redirect(\app::getBaseAppLink('plugins/pLists#app_'.$plugin));
			return true;
		}
	}
	public function deActivePlugin($plugin = null){
		if ( is_null($plugin)){
			Response::redirect(\app::getBaseAppLink('plugins/pLists'));
			return false;
		}
		$appStatus = cache::get('pluginStatus', $plugin ,'paymentCms');
		if ( $appStatus == 'active' ){
			$this->changeCacheOfAppStatus($plugin,'deActive','pluginStatus');
			Response::redirect(\app::getBaseAppLink('plugins/pLists/appUninstalled#app_'.$plugin));
			return true;
		} else {
			Response::redirect(\app::getBaseAppLink('plugins/pLists#app_'.$plugin));
			return true;
		}
	}

	public function deActive($app = null){
		if ( is_null($app)){
			Response::redirect(\app::getBaseAppLink('plugins/lists'));
			return false;
		}
		$appStatus = cache::get('appStatus', $app ,'paymentCms');
		if ( $appStatus == 'active' and ( ! ($app == 'admin' or $app == 'core' or $app == 'api' or $app == 'user'))){
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
				$querys = $this->generateQueryDropTable($appData['db']);
				model::transaction();
				try {
					$hasError = false ;
					foreach ( $querys as $tabelName => $query){
						if ( model::queryUnprepared($query) === false) {
							$hasError = true ;
						}
					}
					if ( $hasError === false ){
						model::commit();
						file::removedir(payment_path.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.$app);
						$this->changeCacheOfAppStatus($app,null);
						Response::redirect(\app::getBaseAppLink('plugins/lists/appUninstalled#app_'.$app));
						return true;
					} else {
						model::rollback();
						Response::redirect(\app::getBaseAppLink('plugins/lists/pleaseTryAGain#app_'.$app));
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

	public function uninstallLocalPlugin($plugin = null){
		if ( is_null($plugin)){
			Response::redirect(\app::getBaseAppLink('plugins/pLists'));
			return false;
		}
		$file_name = payment_path.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$plugin.DIRECTORY_SEPARATOR.'info.php';
		if ( file_exists($file_name) ) {
			file::removedir(payment_path.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$plugin);
			$this->changeCacheOfAppStatus($plugin,null,'pluginStatus');
			Response::redirect(\app::getBaseAppLink('plugins/pLists/appUninstalled#app_'.$plugin));
			return true;
		} else {
			Response::redirect(\app::getBaseAppLink('plugins/pLists/cantFindInfo/'.urlencode($file_name).'#app_'.$app));
			return false;
		}
	}

	public function install($massage = null){
		if ( $massage == 'extractError')
			$this->alert('danger' , '', rlang('extractError') );
		if ( $massage == 'downloadError')
			$this->alert('danger' , '', rlang('downloadError') );
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
			//		$d="https://www.paymentcms.ir/api/update/version";
			$appsApiLinks ="https://www.paymentcms.ir/api/apps/listApps";
			$localData = array(
				'page' => $get['page'] ,
				'perEachPage' => $get['perEachPage'] ,
				'name' => $get['name'] ,
				'version' => PCVERSION ,
				'siteUrl' => \App::getCurrentBaseLink(),
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
		$query = [];
		if ( is_array($tables) ) {
			$configDataBase = require payment_path. 'core'.DIRECTORY_SEPARATOR. 'config.php';
			foreach ( $tables as $tableName => $tableData) {
				$query[$tableName] = 'CREATE TABLE IF NOT EXISTS `'.$configDataBase['_dbTableStartWith'].$tableName.'` ('.chr(10) ;
				foreach ( $tableData['fields'] as $fieldName => $fieldData) {
					$query[$tableName] .= '  `'.$fieldName.'` '.$fieldData.','.chr(10) ;
				}
				if ( isset($tableData['PRIMARY KEY']) and is_array($tableData['PRIMARY KEY']) and ! is_null($tableData['PRIMARY KEY'])) {
					foreach ($tableData['PRIMARY KEY'] as $fieldName) {
						$query[$tableName] .= ' PRIMARY KEY (`'.$fieldName.'`) USING BTREE,'.chr(10);
					}
				}
				if ( isset($tableData['KEY']) and is_array($tableData['KEY']) and ! is_null($tableData['KEY'])) {
					foreach ($tableData['KEY'] as $fieldName) {
						$query[$tableName] .= ' KEY `'.$fieldName.'` (`'.$fieldName.'`),'.chr(10);
					}
				}
				if ( isset($tableData['REFERENCES']) and is_array($tableData['REFERENCES']) and ! is_null($tableData['REFERENCES'])) {
					foreach ($tableData['REFERENCES'] as $fieldName => $fieldData) {
						$query[$tableName] .= 'FOREIGN KEY (`'.$fieldName.'`) REFERENCES `'.$configDataBase['_dbTableStartWith'].$fieldData['table'].'`(`'.$fieldData['column'].'`) ON DELETE '.$fieldData['on_delete'].' ON UPDATE '.$fieldData['on_update'].','.chr(10);
					}
				}
				$query[$tableName] = strings::deleteWordLastString($query[$tableName],','.chr(10) ).chr(10);
				$query[$tableName] .= ') ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;'.chr(10).chr(10);

			}
		}
		return $query ;
	}
	private function generateQueryDropTable($tables){
		$query[] = 'SET FOREIGN_KEY_CHECKS = 0;';
		if ( is_array($tables) ) {
			$configDataBase = require_once payment_path. 'core'.DIRECTORY_SEPARATOR. 'config.php';
			foreach ( $tables as $tableName => $tableData) {
				$query[$tableName] .= 'DROP TABLE IF EXISTS `'.$configDataBase['_dbTableStartWith'].$tableName.'` ;'.chr(10) ;
			}
		}
		$query[] = 'SET FOREIGN_KEY_CHECKS = 1;';
		return $query ;
	}
	private function changeCacheOfAppStatus($app , $status , $name = 'appStatus'){
		$appStatus = cache::get($name, null  ,'paymentCms');
		if ( $status == null )
			unset($appStatus[$app]);
		else
			$appStatus[$app] = $status ;
		cache::save($appStatus,$name , PHP_INT_MAX , 'paymentCms');
	}
}