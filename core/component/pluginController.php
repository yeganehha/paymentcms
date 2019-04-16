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
	protected function model($model = null , $searchVariable = null , $searchWhereClaus = 'id = ? ') {
		if ( $model == null )
			$model = $this->model;
		$model = 'paymentCms\model\\'.$model ;
		if (class_exists($model)) {
			return new $model($searchVariable,$searchWhereClaus) ;
		} else {
			App\core\controller\httpErrorHandler::E500($model);
			exit;
		}

	}

}