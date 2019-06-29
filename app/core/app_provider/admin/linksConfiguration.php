<?php


namespace App\core\app_provider\admin;


use paymentCms\component\cache;
use paymentCms\component\model;
use paymentCms\component\request;
use paymentCms\component\Response;
use paymentCms\component\strings;
use paymentCms\component\validate;
use paymentCms\model\api;
use paymentCms\model\invoice;

/**
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 3/24/2019
 * Time: 10:15 AM
 * project : paymentCMS
 * virsion : 0.0.0.1
 * update Time : 3/24/2019 - 10:15 AM
 * Discription of this Page :
 */


if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


class linksConfiguration extends \controller {
	public function index($action = null){
		if ( $action == 'insertDone' )
			$this->alert('success', null,rlang(['add','link','successfully','was']));
		elseif ( $action == 'deleteDone')
			$this->alert('success', null,rlang(['delete','link','successfully','was']));
		elseif ( $action == 'editDone')
			$this->alert('success', null,rlang(['edit','link','successfully','was']));
		/* @var \paymentCms\model\apps_link $model */
		$model = parent::model('apps_link');
		$links = $model->search(null,null , 'apps_link' ,'*',['column'=>'link','type'=>'desc'] );
		$apps = \app::appsList();
		$this->mold->set('links' , $links);
		$this->mold->set('apps' , $apps);
		$this->mold->path('default', 'core');
		$this->mold->view('apps_link.mold.html');
		$this->mold->set('activeMenu' , 'uniqueLinks');
		$this->mold->setPageTitle(rlang('uniqueLinks'));
	}

	public function insert(){
		$get = request::post('link,app' ,null);
		$rules = [
			"app" => ["required", rlang('page')],
			"link" => ["required|url", rlang('page')],
		];
		$valid = validate::check($get, $rules);
		if ($valid->isFail()){
			$this->alert('danger','',$valid->errorsIn());
			$this->index();
			return false ;
		}
		$apps = \app::appsList();
		if (  ! in_array($get['app'],$apps) ){
			$this->alert('danger','',rlang('appCanNotFound'));
			$this->index();
			return false ;
		}

		if ( strings::strLastHas($get['link'],'/') )
			$get['link'] = strings::deleteWordLastString($get['link'],'/');

		/* @var \paymentCms\model\apps_link $model */
		$model = parent::model('apps_link');
		$links = $model->search($get['link'],' link = ? ' );
		if (  $links !== true ){
			$this->alert('danger','',rlang('doNotInsertUniqueLink'));
			$this->index();
			return false ;
		}

		$model->setLink($get['link']);
		$model->setApp($get['app']);
		$result = $model->insertToDataBase();
		if ( $result ){
			$this->generateAllLinks();
			Response::redirect(\App::getBaseAppLink('linksConfiguration/insertDone','admin'));
			return true ;
		} else {
			$this->alert('danger','',rlang('pleaseTryAGain'));
			$this->index();
			return false ;
		}
	}

	public function edit($editId = null){
		if ( $editId == null ){
			Response::redirect(\App::getBaseAppLink('linksConfiguration','admin'));
			return true ;
		}
		$get = request::post('link' ,null);
		$rules = [
			"link" => ["required|url", rlang('page')],
		];
		$valid = validate::check($get, $rules);
		if ($valid->isFail()){
			$this->alert('danger','',$valid->errorsIn());
			$this->index();
			return false ;
		}

		if ( strings::strLastHas($get['link'],'/') )
			$get['link'] = strings::deleteWordLastString($get['link'],'/');

		/* @var \paymentCms\model\apps_link $model */
		$model = parent::model('apps_link' , $editId);
		if ( $model->getAppsLinkId() == null ){
			Response::redirect(\App::getBaseAppLink('linksConfiguration','admin'));
			return true ;
		}
		$links = $model->search([$get['link'],$editId],' link = ? and apps_linkId != ? ' );
		if (  $links !== true ){
			$this->alert('danger','',rlang('doNotInsertUniqueLink'));
			$this->index();
			return false ;
		}

		$model->setLink($get['link']);
		$result = $model->upDateDataBase();
		if ( $result ){
			$this->generateAllLinks();
			if ( $model->getApp() == 'admin' )
				Response::redirect($get['link'].'/linksConfiguration/editDone');
			else
				Response::redirect(\App::getBaseAppLink('linksConfiguration/editDone','admin'));
			return true ;
		} else {
			$this->alert('danger','',rlang('pleaseTryAGain'));
			$this->index();
			return false ;
		}
	}

	public function delete($deleteId = null){
		if ( $deleteId == null ){
			Response::redirect(\App::getBaseAppLink('linksConfiguration','admin'));
			return true ;
		}
		/* @var \paymentCms\model\apps_link $model */
		$model = parent::model('apps_link' , $deleteId);
		if ( $model->getAppsLinkId() == null ){
			Response::redirect(\App::getBaseAppLink('linksConfiguration','admin'));
			return true ;
		}
		$result = $model->deleteFromDataBase();
		if ( $result ){
			$this->generateAllLinks();
			Response::redirect(\App::getBaseAppLink('linksConfiguration/deleteDone','admin'));
			return true ;
		} else {
			$this->alert('danger','',rlang('pleaseTryAGain'));
			$this->index();
			return false ;
		}
	}

	private function generateAllLinks(){
		$links = model::searching(null, null, 'apps_link');
		return cache::save($links, 'apps_link', PHP_INT_MAX, 'paymentCms');
	}
}