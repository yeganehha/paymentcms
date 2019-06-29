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
                                            <h4 class=\"card-title float-right m-3\">به سامانه مدیریت محتوای پرداختی خوش آمدید!</h4>
                                        </div>
                                        <div class=\"card-body text-right text-secondary\">
<p>از این که نرمافزار کدباز ( open source ) ما را جهت مدیریت وبسایت خودتان انتخاب نموده اید، متشکریم!</p>
<p>&nbsp;</p>
<p><strong>در اجرای صفحه با خطا مواجه می گردید ؟</strong> این نرمافزار با مواجه شدن به خطا های غیر منتظره، به صورت خودکار تیم پشتیبانی را درجریان مشکل به وجود آمده قرار می دهد. در همین راستا هسته اصلی این نرمافزار و اپلیکیشن های متصل به آن به صورت روزانه بروزرسانی می شوند. پس با خیالی آسوده به توسعه قویی تر وبسایت خود بپردازید و وظیفه برقراری امنیت و افزایش سرعت وبسایتتان را به ما بسپارید.</p>
<p>&nbsp;</p>
<p>مطالب و اپلیکیشن های ما را روزانه دنبال کنید و روزانه امکانات وبسایتتان را افزایش دهید.</p>
<p>در صورتی که توسعه دهنده Back End ویا Front End  هستید شک نکنید که ما مایل به همکاری با شما هستیم.</p>
<p>در کنار شما برای پیاده سازی ایده های شما هستیم!</p>
<p><a href=\"https://www.paymentcms.ir\" target=\"_blank\" rel=\"noopener\">تیم پشتیبانی Payment CMS</a></p>
                                        </div>
                                        <div class=\"card-footer\">
                                        </div>
                                    </div>
                                    " , 'panel2' => null ] );
	}

}