<?php


namespace App\log\app_provider\admin;


use App\core\controller\httpErrorHandler;
use paymentCms\component\file;
use paymentCms\component\model;
use paymentCms\component\request;
use paymentCms\component\Response;
use paymentCms\component\session;
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


class logs extends \controller {
	public function index(){
		$this->lists();
	}
	public function lists() {
		$get = request::post('page=1,perEachPage=25,content,userId,StartTime,EndTime,ip,viewPage' ,null);
		$rules = [
			"page" => ["required|match:>0", rlang('page')],
			"perEachPage" => ["required|match:>0|match:<501", rlang('page')],
		];
		$valid = validate::check($get, $rules);
		$value = array( );
		$variable = array( );
		if ($valid->isFail()){
			//TODO:: add error is not valid data

		} else {
			if ( $get['content'] != null ) {
				$value[] = '%'.$get['content'].'%' ;
				$value[] = '%'.$get['content'].'%' ;
				$value[] = '%'.$get['content'].'%' ;
				$variable[] = ' description LIKE ? ' ;
			}
			if ( $get['userId'] != null ) {
				$value[] = $get['userId'] ;
				$variable[] = ' userId = ? ';
			}
			if ( $get['ip'] != null ) {
				$value[] = $get['ip'] ;
				$variable[] = ' ip = ? ';
			}
			if ( $get['StartTime'] != null and $get['EndTime'] == null) {
				$value[] = date('Y-m-d H:i:s' , $get['StartTime'] / 1000 ) ;
				$variable[] = ' activity_time > ? ';
			} elseif ( $get['StartTime'] == null and $get['EndTime'] != null) {
				$value[] = date('Y-m-d H:i:s' , $get['EndTime'] / 1000 ) ;
				$variable[] = ' activity_time < ? ';
			} elseif ( $get['StartTime'] != null and $get['EndTime'] != null)  {
				$value[] = date('Y-m-d H:i:s' , $get['StartTime'] / 1000 ) ;
				$value[] = date('Y-m-d H:i:s' , $get['EndTime'] / 1000 ) ;
				$variable[] = ' (activity_time BETWEEN ? AND ?) ';
			}
		}
		if ( $get['viewPage'] != 'active'){
			$value[] = 'view_webSite_page';
			$variable[] = 'log_name != ? ';
		}
		$model = parent::model('log');
		$numberOfAll = ($model->search( (array) $value  , ( count($variable) == 0 ) ? null : implode(' and ' , $variable) , null, 'COUNT(logId) as co' )) [0]['co'];
		$pagination = parent::pagination($numberOfAll,$get['page'],$get['perEachPage']);
		$search = $model->search( (array) $value  , ( ( count($variable) == 0 ) ? null : implode(' and ' , $variable) )  , null, '*'  , ['column' => 'logId' , 'type' =>'desc'] , [$pagination['start'] , $pagination['limit'] ] );
		$this->mold->path('default', 'log');
		$this->mold->view('logList.mold.html');
		$this->mold->setPageTitle(rlang('logs'));
		$this->mold->set('activeMenu' , 'logs');
		$this->mold->set('logs' , $search);
	}
	public function insert(){
		if ( request::isPost() ) {
			$this->checkData();
		}
		$files = file::get_files_by_pattern(__DIR__.'/../../theme/'.'default'.'/','*.content.mold.html');
		$template = [];
		for ( $i = 0 ; $i < count($files) ; $i++){
			$files[$i] = strings::deleteWordLastString($files[$i],'.content.mold.html');
			$files[$i] = strings::deleteWordFirstString($files[$i],__DIR__.'/../../theme/'.'default'.'/');
			$template[$files[$i]] = rlang($files[$i].'_templateFile');
			if ( $template[$files[$i]] == null)
				$template[$files[$i]] = $files[$i] ;
		}
		unset($files);
		$this->mold->set('template',$template);
		$this->mold->path('default', 'landing');
		$this->mold->view('landingPageEditor.mold.html');
		$this->mold->path('default');
		$this->mold->setPageTitle(rlang(['add','page']));
	}
	public function edit($pageId,$updateStatus = null){
		if ( request::isPost() ) {
			$this->checkData($pageId);
		}
		/* @var \App\landing\model\landingPage $page */
		$page = $this->model('landingPage' , $pageId );
		if ( $page->getLandingPageId() != $pageId ){
			httpErrorHandler::E404();
			return false ;
		}
		if ( $updateStatus == 'updateDone') {
			$this->alert('success' , '',rlang('editPageSuccessFully'));
			$this->mold->set('activeTab','edit');
		} elseif ( $updateStatus == 'insertDone') {
			$this->alert('success' , '',rlang('insertPageSuccessFully'));
		}


		$files = file::get_files_by_pattern(__DIR__.'/../../theme/'.'default'.'/','*.content.mold.html');
		$template = [];
		for ( $i = 0 ; $i < count($files) ; $i++){
			$files[$i] = strings::deleteWordLastString($files[$i],'.content.mold.html');
			$files[$i] = strings::deleteWordFirstString($files[$i],__DIR__.'/../../theme/'.'default'.'/');
			$template[$files[$i]] = rlang($files[$i].'_templateFile');
			if ( $template[$files[$i]] == null)
				$template[$files[$i]] = $files[$i] ;
		}
		unset($files);
		$this->mold->set('template',$template);


		$this->mold->set('page',$page);
		$this->mold->path('default', 'landing');
		$this->mold->view('landingPageEditor.mold.html');
		$this->mold->setPageTitle(rlang(['profile','page']));
		return $page;
	}

	/**
	 * @param null $userId
	 *
	 * @return bool
	 * [no-access]
	 */
	public function checkData($pageId = null){
		$form = request::post('id=0,name,metaDescription,content,template,default');
		$rules = [
			'id' => ['int|match:>=0'	, rlang('id')],
			'name' => ['required'	, rlang('name')],
			'content' => ['required'	, rlang('content')],
			'template' => ['required'	, rlang('template')],
		];
		$valid = validate::check($form, $rules);
		if ($valid->isFail()){
			$this->alert('warning' , null,$valid->errorsIn(),'error');
			return false;
		} else {
			model::transaction();
			/* @var \App\landing\model\landingPage $page */
			if ( $pageId != null ) {
				$page = $this->model('landingPage' , $pageId );
				if ( $page->getLandingPageId() != $pageId){
					httpErrorHandler::E404();
					return false;
				}
			} else
				$page = $this->model('landingPage' );

			$page->setName($form['name']);
			$page->setMetaDescription($form['metaDescription']);
			$page->setTemplate($form['content']);
			$page->setTemplateName($form['template']);
			if ( $form['default'] == 'active') {
				$resultUpdateDeActive = $page->deActiveAllDefault();
				if ( $resultUpdateDeActive == false){
					model::rollback();
					$this->alert('warning' , null,rlang('pleaseTryAGain'),'error');
					return false;
				}
				$page->setUseAsDefault(1);
			} else {
				$page->setUseAsDefault(0);
			}
			if ( $pageId != null )
				$result = $page->upDateDataBase();
			else
				$result = $page->insertToDataBase();

			if ( $result === false ) {
				model::rollback();
				$this->alert('warning' , null,rlang('pleaseTryAGain'),'error');
				return false;
			}
			model::commit();
			if ( $pageId != null )
				Response::redirect(\App::getBaseAppLink('landingPage/edit/' . $pageId . '/updateDone', 'admin'));
			else
				Response::redirect(\App::getBaseAppLink('landingPage/edit/' . $page->getLandingPageId() . '/insertDone', 'admin'));
			return true;
		}
	}
}