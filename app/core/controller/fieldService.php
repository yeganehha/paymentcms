<?php


namespace App\core\controller;



/**
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 4/1/2019
 * Time: 5:27 PM
 * project : paymentCMS
 * virsion : 0.0.0.1
 * update Time : 4/1/2019 - 5:27 PM
 * Discription of this Page :
 */


if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


class fieldService extends \controller {

	public static function getFieldsToEdit($serviceId,$serviceType , &$mold = null ) {
		$return = \App\core\app_provider\api\fields::getFieldsToEdit($serviceId,$serviceType);
		if ( $mold != null and isset($return['result']) ){
			$getPath = $mold->getPath();
			$mold->path('default', 'core');
			$mold->view('editAbleFields.mold.html');
			$mold->set('numberOfFields',count($return['result']));
			$mold->set('fields',$return['result']);
			$mold->path($getPath['folder'], $getPath['app']);
		}
		return ( isset($return['result']) ? $return['result'] : [] ) ;
	}

	public static function getFieldsToFillOut($serviceId,$serviceType, &$mold = null ) {
		$return =  \App\core\app_provider\api\fields::getFieldsToEdit($serviceId,$serviceType , ['admin' , 'invisible'] , true);
		if ( $mold != null and isset($return['result']) ){
			$getPath = $mold->getPath();
			$mold->path('default', 'core');
			$mold->view('fillOutFields.mold.html');
			$mold->set('fields',$return['result']);
			$mold->path($getPath['folder'], $getPath['app']);
		}
		return $return ;
	}

	public static function updateFields($serviceId,$serviceType,$fields,$deletedFields=null){
		return \App\core\app_provider\api\fields::updateFields($serviceId,$serviceType , $fields , $deletedFields);
	}
}