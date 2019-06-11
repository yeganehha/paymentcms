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


class pluginController {

	protected $model ;
	protected $mold ;
	protected $menu ;

	public function __construct(&$mold,&$menu = null) {
		$this->mold = $mold;
		$this->menu = $menu;
	}

	protected function creatView(){
		$mold = new Mold();
		$mold->path('');
		$mold->cache(10);
		$mold->offAutoCompile();
		$mold->set('direction' , 'rtl');
		$mold->set('text_align' , 'right');
		$mold->set('float' , 'right');
		return $mold ;
	}
	/**
	 * @param null   $model
	 * @param null   $searchVariable
	 * @param string $searchWhereClaus
	 *
	 * @return \App\model\model
	 */
	protected function model($modelName = null , $searchVariable = null , $searchWhereClaus = 'id = ? ') {
		if ( is_array($modelName) and count($modelName) == 2 ) {
			$app = $modelName[0];
			$modelName = $modelName[1];
		}
		if ( $modelName == null )
			$modelName = $this->model;
		if ( empty(\app::getAppProvider()) and ! isset($app))
			$model = 'App\\'.\app::getApp().'\model\\'.$modelName ;
		elseif ( ! isset($app) )
			$model = 'App\\'.\app::getAppProvider().'\model\\'.$modelName ;
		elseif ( isset($app))
			$model = 'App\\'.$app.'\model\\'.$modelName ;

		if (class_exists($model)) {
			if ( $searchWhereClaus == null )
				return new $model($searchVariable) ;
			else
				return new $model($searchVariable,$searchWhereClaus) ;
		} else {
			$model = 'paymentCms\model\\'.$modelName ;
			if (class_exists($model)) {
				if ( $searchWhereClaus == null )
					return new $model($searchVariable) ;
				else
					return new $model($searchVariable,$searchWhereClaus) ;
			} else {
				App\core\controller\httpErrorHandler::E500($model);
				exit;
			}
		}
	}

}