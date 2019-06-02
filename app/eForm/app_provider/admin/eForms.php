<?php


namespace App\eForm\app_provider\admin;


use App\core\controller\fieldService;
use App\core\controller\httpErrorHandler;
use App\eForm\model\eform;
use paymentCms\component\file;
use paymentCms\component\model;
use paymentCms\component\request;
use paymentCms\component\Response;
use paymentCms\component\session;
use paymentCms\component\strings;
use paymentCms\component\validate;

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


class eForms extends \controller {
	public function index(){
//		$this->lists();
	}
	public function all() {
		$get = request::post('page=1,perEachPage=25,content,published,public' ,null);
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
				$variable[] = ' name LIKE ? ' ;
			}
			if ( $get['published'] == 'active') {
				$value[] = 1 ;
				$variable[] = ' published = ? ';
			}
			if ( $get['public'] == 'active') {
				$value[] = 1 ;
				$variable[] = ' public = ? ';
			}
		}
		$model = parent::model('eform');
		$numberOfAll = ($model->search( (array) $value  , ( count($variable) == 0 ) ? null : implode(' and ' , $variable) , null, 'COUNT(formId) as co' )) [0]['co'];
		$pagination = parent::pagination($numberOfAll,$get['page'],$get['perEachPage']);
		$search = $model->search( (array) $value  , ( ( count($variable) == 0 ) ? null : implode(' and ' , $variable) )  , null, 'formId,name,published,public'  , ['column' => 'formId' , 'type' =>'desc'] , [$pagination['start'] , $pagination['limit'] ] );
		$this->mold->path('default', 'eForm');
		$this->mold->view('eFormAdminList.mold.html');
		$this->mold->setPageTitle(rlang('eForms'));
		$this->mold->set('activeMenu' , 'allForms');
		$this->mold->set('forms' , $search);
	}
	public function lists() {
		$get = request::post('page=1,perEachPage=25,content' ,null);
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
				$variable[] = ' name LIKE ? ' ;
			}
		}
		/* @var eform $model */
		$model = parent::model('eform');
		model::join('eformfilled f' , 'f.formId = e.formId and f.userId = '.session::get('userAppLoginInformation')['userId'], "left" );
		$value[] = '%,'.session::get('userAppLoginInformation')['user_group_id'].',%' ;
		$variable[] = ' ( ( e.oneTime = 1 and f.fillId IS NULL ) or e.oneTime = 0 ) and published = 1 and public = 1 and access LIKE ? ';
		$numberOfAll = ($model->search( (array) $value  , ( count($variable) == 0 ) ? null : implode(' and ' , $variable) , 'eform e', 'COUNT(f.formId) as co' , null,null,'e.formId')) [0]['co'];
		$pagination = parent::pagination($numberOfAll,$get['page'],$get['perEachPage']);
		model::join('eformfilled f' , 'f.formId = e.formId and f.userId = '.session::get('userAppLoginInformation')['userId'], "left" );
		$value[] = '%,'.session::get('userAppLoginInformation')['user_group_id'].',%' ;
		$variable[] = ' ( ( e.oneTime = 1 and f.fillId IS NULL ) or e.oneTime = 0 ) and published = 1 and public = 1 and access LIKE ? ';
		$search = $model->search( (array) $value  , ( ( count($variable) == 0 ) ? null : implode(' and ' , $variable) )  , 'eform e', 'e.formId,e.name,e.oneTime,f.fillStart,f.fillId'  , ['column' => 'e.formId' , 'type' =>'desc'] , [$pagination['start'] , $pagination['limit'] ] ,'e.formId' );
		$this->mold->path('default', 'eForm');
		$this->mold->view('eFormUserList.mold.html');
		$this->mold->setPageTitle(rlang(['eForms' , 'pending']));
		$this->mold->set('activeMenu' , 'allFormsNotAnswer');
		$this->mold->set('forms' , $search);
	}
	public function insert(){
		if ( request::isPost() ) {
			$this->checkData();
		}
		$files = file::get_files_by_pattern(__DIR__.'/../../theme/'.'default'.'/','*.form.mold.html');
		$template = [];
		for ( $i = 0 ; $i < count($files) ; $i++){
			$files[$i] = strings::deleteWordLastString($files[$i],'.form.mold.html');
			$files[$i] = strings::deleteWordFirstString($files[$i],__DIR__.'/../../theme/'.'default'.'/');
			$template[$files[$i]] = rlang($files[$i].'_templateFile');
			if ( $template[$files[$i]] == null)
				$template[$files[$i]] = $files[$i] ;
		}
		unset($files);
		/* @var \App\user\model\user_group $model */
		$model = $this->model(['user','user_group']);
		$access = $model->search(null,null);
		$this->mold->set('access',$access);
		$this->mold->set('template',$template);
		$this->mold->path('default', 'eForm');
		$this->mold->view('formEditor.mold.html');
		$this->mold->path('default');
		$this->mold->setPageTitle(rlang(['add','eForm']));
		$this->mold->set('activeMenu' , 'newForms');
	}
	public function edit($formId,$updateStatus = null){
		if ( request::isPost() ) {
			$this->checkData($formId);
		}

		/* @var \App\eForm\model\eform $form */
		$form = $this->model('eform' , $formId );
		if ( $form->getFormId() != $formId ){
			httpErrorHandler::E404();
			return false ;
		}
		if ( $updateStatus == 'updateDone') {
			$this->alert('success' , '',rlang('editEFormSuccessFully'));
			$this->mold->set('activeTab','edit');
		} elseif ( $updateStatus == 'insertDone') {
			$this->alert('success' , '',rlang('insertEFromSuccessFully'));
		}

		$files = file::get_files_by_pattern(__DIR__.'/../../theme/'.'default'.'/','*.form.mold.html');
		$template = [];
		for ( $i = 0 ; $i < count($files) ; $i++){
			$files[$i] = strings::deleteWordLastString($files[$i],'.form.mold.html');
			$files[$i] = strings::deleteWordFirstString($files[$i],__DIR__.'/../../theme/'.'default'.'/');
			$template[$files[$i]] = rlang($files[$i].'_templateFile');
			if ( $template[$files[$i]] == null)
				$template[$files[$i]] = $files[$i] ;
		}
		unset($files);
		/* @var \App\user\model\user_group $model */
		$model = $this->model(['user','user_group']);
		$access = $model->search(null,null);

		$accessSelect = array_filter((array) explode(',' , $form->getAccess()));

		fieldService::getFieldsToEdit($form->getFormId(),'eForm',$this->mold);
		$this->mold->set('access',$access);
		$this->mold->set('accessSelect',$accessSelect);
		$this->mold->set('template',$template);
		$this->mold->set('form',$form);
		$this->mold->path('default', 'eForm');
		$this->mold->view('formEditor.mold.html');
		$this->mold->setPageTitle(rlang(['edit','eForm']));
		return $form;
	}

	/**
	 * @param null $userId
	 *
	 * @return bool
	 * [no-access]
	 */
	private function checkData($eFormId = null){
		$form = request::post('id=0,name,description,lastNote,templateName,access,published,oneTime,public,showHistory,moreField,deleteField');
		$rules = [
			'id' => ['int|match:>=0'	, rlang('id')],
			'name' => ['required'	, rlang('name')],
			'access' => ['required'	, rlang('access')],
			'templateName' => ['required'	, rlang('template')],
		];
		$valid = validate::check($form, $rules);
		if ($valid->isFail()){
			$this->alert('warning' , null,$valid->errorsIn(),'error');
			return false;
		} else {
			model::transaction();
			/* @var \App\eForm\model\eform $eForm */
			if ( $eFormId != null ) {
				$eForm = $this->model('eform', $eFormId);
				if ( $eForm->getFormId() != $eFormId){
					httpErrorHandler::E404();
					return false;
				}
			} else
				$eForm = $this->model('eform' );

			$eForm->setName($form['name']);
			$eForm->setLastNote($form['lastNote']);
			$eForm->setDescription($form['description']);
			$eForm->setTemplateName($form['templateName']);
			$eForm->setAccess(','.implode(',',$form['access']).',');
			if ( $form['published'] == 'active') {
				$eForm->setPublished(1);
			} else {
				$eForm->setPublished(0);
			}
			if ( $form['oneTime'] == 'active') {
				$eForm->setOneTime(1);
			} else {
				$eForm->setOneTime(0);
			}
			if ( $form['public'] == 'active') {
				$eForm->setPublic(1);
			} else {
				$eForm->setPublic(0);
			}
			if ( $form['showHistory'] == 'active') {
				$eForm->setShowHistory(1);
			} else {
				$eForm->setShowHistory(0);
			}
			if ( $eFormId != null )
				$result = $eForm->upDateDataBase();
			else
				$result = $eForm->insertToDataBase();

			if ( $result === false ) {
				$this->alert('warning' , null,rlang('pleaseTryAGain'),'error');
				return false;
			}
			if ( $eFormId != null ) {
				$resultUpdateField = fieldService::updateFields($eForm->getFormId(),'eForm' ,$form['moreField'],$form['deleteField']);
				if ( ! $resultUpdateField['status'] ) {
					model::rollback();
					$this->alert('warning' , null, rlang('pleaseTryAGain').'<br>'.$resultUpdateField['massage'],'error');
					return false;
				}
				model::commit();
				Response::redirect(\App::getBaseAppLink('eForms/edit/' . $eFormId . '/updateDone', 'admin'));
			} else {
				model::commit();
				Response::redirect(\App::getBaseAppLink('eForms/edit/' . $result['result'] . '/insertDone', 'admin'));
			}
			return true;
		}
	}
}