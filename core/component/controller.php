<?php
/**
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 3/24/2019
 * Time: 7:58 PM
 * project : paymentCMS
 * version : 0.0.0.1
 * update Time : 3/24/2019 - 7:58 PM
 * Description of this Page :
 */

if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


class controller {

	protected $app ;
	protected $model ;
	protected function model($model = null , $searchVariable = null , $searchWhereClaus = 'id = ? ') {
		if ( $model == null )
			$model = $this->model;
		if (file_exists(payment_path.'app'.DIRECTORY_SEPARATOR.$this->app.DIRECTORY_SEPARATOR . 'model'.DIRECTORY_SEPARATOR. $model . '.php')) {
			$model = 'App\\'.$this->app.'\model\\'.$model ;
			if (class_exists($model)) {
				return new $model($searchVariable,$searchWhereClaus) ;
			} else {
				App\core\controller\httpErrorHandler::E500();
				exit;
			}
		} else {
			App\core\controller\httpErrorHandler::E500();
			exit;
		}
	}
}