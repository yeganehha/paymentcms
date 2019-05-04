<?php

namespace App\paymentServer\app_provider\api;

use App\paymentServer\controller\statistics;
use paymentCms\component\model;
use paymentCms\component\mold\Mold;
use paymentCms\component\request;
use paymentCms\component\Response;
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


class apps extends \App\api\controller\innerController  {

	public function listApps() {
		statistics::addStatistics();
		$get = request::post('page=1,perEachPage=25,name' ,null);
		$rules = [
			"page" => ["required|match:>0", rlang('page')],
			"perEachPage" => ["required|match:>0|match:<501", rlang('page')],
		];
		$valid = validate::check($get, $rules);
		$value = array( );
		$variable = array( );
		if ($valid->isFail()){
			self::jsonError(rlang('pleaseTryAGain') , 203);
		} else {
			if ( $get['name'] != null ) {
				$value[] = '%'.$get['name'].'%' ;
				$value[] = '%'.$get['name'].'%' ;
				$variable[] = 'name LIKE ?' ;
				$variable[] = 'author LIKE ?' ;
			}
			/* @var \App\paymentServer\model\plugin_list $pluginModel */
			$pluginModel = $this->model('plugin_list');
			$numberOfAll = ($pluginModel->search( (array) $value  , ( count($variable) == 0 ) ? null : implode('or' , $variable) , null, 'COUNT(name) as co' )) [0]['co'];
			if ( $get['page'] < 1 ) $get['page'] = 1 ;
			$total = ceil($numberOfAll / $get['perEachPage'] ) ;
			if ( $get['page'] > $total ) $get['page'] = $total ;
			$pagination = ['start' => ($get['page'] -1 ) * $get['perEachPage'] , 'limit' => (int) $get['perEachPage'] , 'totalPage' => $total  , 'totalRecords' =>$numberOfAll];
			model::join('plugin_statistics s', 'l.name = s.pluginName');
			$pagination['plugins'] = $pluginModel->search(null, null, 'plugin_list l', 'l.* , count(*) as count', ['column' => 'count', 'type' => 'desc'],  [$pagination['start'] , $pagination['limit'] ], 'l.name');
			self::json($pagination);
		}
	}

}