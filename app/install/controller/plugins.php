<?php


namespace App\install\controller;


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

	private static $prefix;

	public static function installLocal($app){
		$appStatus = cache::get('appStatus', $app ,'paymentCms');
		if ( $appStatus == null ){
			$file_name = payment_path.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.$app.DIRECTORY_SEPARATOR.'info.php';
			if ( file_exists($file_name) ) {
				$appData = require_once $file_name;
				if ( isset($appData['db']) and !  is_null($appData['db'])) {
					$querys = self::generateQueryCreatTable($appData['db']);
					model::transaction();
					try {
						$hasError = false ;
						foreach ( $querys as $tabelName => $query){
							if ( model::queryUnprepared($query) === false) {
								$hasError = true ;
							}
						}
						if ( $hasError === false ) {
							model::commit();
							return true;
						} else {
							model::rollback();
							return false;
						}
					} catch (Exception $exception){
						model::rollback();
						return false;
					}
				} else {
					model::commit();
					return true;
				}
			} else {
				return false;
			}
		}
		return false;
	}


	private static function generateQueryCreatTable($tables){
		$query = [];
		if ( is_array($tables) ) {
			foreach ( $tables as $tableName => $tableData) {
				$query[$tableName] = 'CREATE TABLE IF NOT EXISTS `'.self::$prefix.$tableName.'` ('.chr(10) ;
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
						$query[$tableName] .= 'FOREIGN KEY (`'.$fieldName.'`) REFERENCES `'.$fieldData['table'].'`(`'.$fieldData['column'].'`) ON DELETE '.$fieldData['on_delete'].' ON UPDATE '.$fieldData['on_update'].','.chr(10);
					}
				}
				$query[$tableName] = strings::deleteWordLastString($query[$tableName],','.chr(10) ).chr(10);
				$query[$tableName] .= ') ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;'.chr(10).chr(10);

			}
		}
		return $query ;
	}
	public static function changeCacheOfAppStatus($apps ){
		return cache::save($apps,'appStatus' , PHP_INT_MAX , 'paymentCms');
	}

	/**
	 * @param mixed $prefix
	 */
	public static function setPrefix($prefix) {
		self::$prefix = $prefix;
	}
}