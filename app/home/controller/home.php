<?php


namespace App\home\controller;


/**
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 3/23/2019
 * Time: 2:05 PM
 * project : paymentCms
 * virsion : 0.0.0.1
 * update Time : 3/23/2019 - 2:05 PM
 * Discription of this Page :
 */


if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


class home extends \controller {


	public function index($serviceId){
		/* @var \paymentCms\model\service $model */
		$model = $this->model('service',$serviceId);
		if ( is_null($model->getServiceId()) ){
			$this->mold->offAutoCompile();
			\App\core\controller\httpErrorHandler::E404();
			return ;
		}
		/* @var \paymentCms\model\field $fieldModel */
		$fieldModel = $this->model('field') ;
		$fields = $fieldModel->search([$serviceId,'admin' , 'invisible' ],'serviceId = ? and status != ? and status != ? order by orderNumber desc');
		if ( $fields === true)
			$fields = [] ;

		foreach ($fields as $key => $field){
			if ( $field['type'] == 'checkbox' or $field['type'] == 'radio' or $field['type'] == 'select' )
				$fields[$key]['values'] = explode(',', $field['values']);
		}

		$this->mold->set('service',$model);
		$this->mold->set('fields',$fields);
		$this->mold->view('home.mold.html');
	}
	public function sd(){
		show(func_get_args());
	}
}