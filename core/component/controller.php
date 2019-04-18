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

use paymentCms\component\file;
use paymentCms\component\menu\menu;
use paymentCms\component\mold\Mold;
use paymentCms\component\strings;

if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


class controller {

	protected $app ;
	protected $model ;
	protected $mold ;
	protected $menu ;
	protected $alert ;

	public function __construct() {
		/* @var paymentCms\component\mold\Mold $mold */
		$mold = new Mold();
		$this->mold = $mold;
		$mold->path('default', \app::getApp());
		$mold->cache(10);
		$mold->header('header.mold.html');
		$mold->footer('footer.mold.html');
		$mold->set('direction' , 'rtl');
		$mold->set('text_align' , 'right');
		$mold->set('float' , 'right');

		$menu = new menu('sideBar') ;
		$this->menu = $menu ;
		$menu->add('dashboard' , rlang('dashboard' ) , app::getBaseAppLink() , 'fa fa-home' );
		$menu->add('users' , rlang(['list','users'] ) , app::getBaseAppLink('users/lists') , 'fa fa-users' );
		$menu->add('services' , rlang('services' ) , app::getBaseAppLink('service/lists') , 'fa fa-shopping-cart' );
		$menu->add('otherFields' , rlang('fields' ) , app::getBaseAppLink('field/lists') , 'fa fa-wpforms' );
		$menu->add('plugins' , rlang('plugins' ) , app::getBaseAppLink('plugins/lists') , 'fa fa-puzzle-piece' );
		$menu->add('configuration' , rlang('configuration' ) , app::getBaseAppLink('configuration') , 'fa fa-cogs' );
		$menu->add('developer' , rlang('developer' ) , app::getBaseAppLink('developer') , 'fa fa-code' );

		$this->callHooks('adminHeaderNavbar',[1,2]);


	}

	/**
	 * @param null   $model
	 * @param null   $searchVariable
	 * @param string $searchWhereClaus
	 *
	 * @return \App\model\model
	 */
	protected function model($model = null , $searchVariable = null , $searchWhereClaus = null) {
		if ( $model == null )
			$model = $this->model;
		$model = 'paymentCms\model\\'.$model ;
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

	protected function pagination($total, $page, $number = 25 ){
		$total = ceil($total / $number ) ;
		$this->mold->set('pagination' , ['total' => $total ,'perEachPage' => $number , 'currentPage' => $page]);
		return ['start' => ($page -1 ) * $number , 'limit' => $number];
	}

	protected function callHooks($hookName,$variable){
		$files1 = file::get_files_by_pattern(payment_path.'plugins'.DIRECTORY_SEPARATOR,'*'.DIRECTORY_SEPARATOR.'hook.php');
		$files2 = file::get_files_by_pattern(payment_path.'app'.DIRECTORY_SEPARATOR,'*'.DIRECTORY_SEPARATOR.'hook.php');
		$files = array_merge($files1,$files2);
		foreach ($files as $file) {
			$temp = explode(DIRECTORY_SEPARATOR, strings::deleteWordLastString($file,DIRECTORY_SEPARATOR.'hook.php')) ;
			$controller = array_pop($temp);
			$aria = end($temp) ;
			$class = $aria.'\\'.$controller.'\hook';
			$method = '_'.$hookName;
			if ( method_exists($class,$method) ){
				if ( $aria == 'plugin')
					$this->mold->path(null,$controller.':plugin');
				else
					$this->mold->path(null,$controller);
				$Object = new $class($this->mold,$this->menu);
				call_user_func_array([$Object,$method],$variable);
			}
		}
		$this->mold->path('default');
	}

	protected function alert($type , $title , $description ,$icon = null , $close = true ){
		if ( $icon != null )
			$temp['icon'] = $icon ;
		if ( $title != null )
			$temp['title'] = $title ;
		$temp['description'] = $description ;
		$temp['type'] = $type ;
		$temp['canClose'] = $close ;
		$this->alert[] = $temp;
	}

	public function __destruct() {
		if ( ! is_null($this->mold) and ! is_null($this->menu))
			$this->mold->set('menu' , $this->menu );
		if ( ! is_null($this->mold) and ! is_null($this->alert))
			$this->mold->set('alert' , $this->alert );
	}
}