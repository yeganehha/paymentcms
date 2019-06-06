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
				$variable[] = 'l.name LIKE ?' ;
				$variable[] = 'l.author LIKE ?' ;
			}
			$value[] = 1 ;
			/* @var \App\paymentServer\model\plugin_list $pluginModel */
			$pluginModel = $this->model('plugin_list');
			$numberOfAll = ($pluginModel->search( (array) $value  , ( count($variable) == 0 ) ? 'l.publish = ?' : implode(' or ' , $variable) . ' and l.publish = ?'  , 'plugin_list l', 'COUNT(name) as co' )) [0]['co'];
			if ( $get['page'] < 1 ) $get['page'] = 1 ;
			$total = ceil($numberOfAll / $get['perEachPage'] ) ;
			if ( $get['page'] > $total ) $get['page'] = $total ;
			$pagination = ['start' => ($get['page'] -1 ) * $get['perEachPage'] , 'limit' => (int) $get['perEachPage'] , 'totalPage' => $total  , 'totalRecords' =>$numberOfAll];
			model::join('plugin_statistics s', 'l.name = s.pluginName');
			$pagination['plugins'] = $pluginModel->search((array) $value, ( count($variable) == 0 ) ? 'l.publish = ?' : implode(' or ' , $variable) . ' and l.publish = ?' , 'plugin_list l', 'l.* , count(*) as count , CONCAT(\''.\App::getCurrentBaseLink('storage/Dl_').'\', l.name , \'_\' , l.version , \'.zip\') as insertLink', ['column' => 'count', 'type' => 'desc'],  [$pagination['start'] , $pagination['limit'] ], 'l.name');
			self::json($pagination);
		}
	}


	public function updateWithNews(){
		statistics::addStatistics();
		self::json( ['update' => [ 'needUpdate' => false , 'lastVersion' => '1.0.0' ] , 'panel1' => "
		<div class=\"card\">
                                        <div class=\"card-header card-header-info card-header-icon \" >
                                            <div class=\"card-icon\" >
                                                <i class=\"material-icons\">info</i>
                                            </div>
                                            <h4 class=\"card-title float-right m-3\">{_ \"help\"}</h4>
                                        </div>
                                        <div class=\"card-body text-right text-secondary\">
                                            <strong class=\"text-info\">{_ \"name\"} :</strong> {_ \"eForm_add_helper_name\"}<br><br>
                                            <strong class=\"text-info\">{_ \"description\"} :</strong> {_ \"eForm_add_helper_description\"}<br><br>
                                            <strong class=\"text-info\">{_ \"afterAnswer\"} :</strong> {_ \"eForm_add_helper_after_description\"}<br><br>
                                            <strong class=\"text-info\">{_ \"template\"} :</strong> {_ \"eForm_add_helper_template\"}<br><br>
                                            <strong class=\"text-info\">{_ \"access\"} :</strong> {_ \"eForm_add_helper_access\"}<br><br>
                                            <strong class=\"text-info\">{_ \"published\"} :</strong> {_ \"eForm_add_helper_published\"}<br><br>
                                            <strong class=\"text-info\">{_ \"oneTime\"} :</strong> {_ \"eForm_add_helper_oneTime\"}<br><br>
                                            <strong class=\"text-info\">{_ \"public\"} :</strong> {_ \"eForm_add_helper_public\"}<br><br>
                                            <strong class=\"text-info\">{_ \"showHistory\"} :</strong> {_ \"eForm_add_helper_showHistory\"}<br><br>
                                        </div>
                                        <div class=\"card-footer\">
                                        </div>
                                    </div>
                                    " , 'panel2' => null ] );
	}

}