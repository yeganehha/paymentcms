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

use paymentCms\component\menu\menu;
use paymentCms\component\mold\Mold;

if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


class controller {

	protected $app ;
	protected $model ;
	protected $mold ;
	protected $menu ;

	public function __construct() {
		/* @var paymentCms\component\mold\Mold $mold */
		$mold = new Mold();
		$this->mold = $mold;
		$mold->path('default');
		$mold->cache(10);
		$mold->header('header.mold.html');
		$mold->footer('footer.mold.html');

		$menu = new menu('sideBar') ;
		$this->menu = $menu ;
		$menu->add('dashboard' , rlang('dashboard' ) , 'home' , 'fa fa-home' );
		$menu->add('payments' , rlang('payments' ) , 'home3' , 'fa fa-home' );
		$menu->after('dashboard','service' , rlang('service' ) , 'home2' , 'fa fa-home' );
		$menu->addChild('payments','best' , rlang('done' ) , 'home2' , 'fa fa-home' );
		$menu->add('best2' , rlang('done' ) , 'best2' , 'fa fa-home' ,null,'payments');
		$menu->add('best23' , rlang('done' ) , 'best23' , 'fa fa-home' ,null,'payments');
		$menu->addChild('best2','best22' , rlang('service' ) , 'best222' , 'fa fa-home' , null );
		$menu->add('best22222' , rlang('service' ) , 'best222' , 'fa fa-home' , null , 'best2');


	}

	protected function model($model = null , $searchVariable = null , $searchWhereClaus = 'id = ? ') {
		if ( $model == null )
			$model = $this->model;
		if (file_exists(payment_path.'app'.DIRECTORY_SEPARATOR.$this->app.DIRECTORY_SEPARATOR . 'model'.DIRECTORY_SEPARATOR. $model . '.php')) {
			$model = 'App\\'.$this->app.'\model\\'.$model ;
			if (class_exists($model)) {
				return new $model($searchVariable,$searchWhereClaus) ;
			} else {
				App\core\controller\httpErrorHandler::E500($model);
				exit;
			}
		} else {
			App\core\controller\httpErrorHandler::E500(payment_path.'app'.DIRECTORY_SEPARATOR.$this->app.DIRECTORY_SEPARATOR . 'model'.DIRECTORY_SEPARATOR. $model . '.php');
			exit;
		}
	}

	public function __destruct() {
		$this->mold->set('menu' , $this->menu );
	}
}