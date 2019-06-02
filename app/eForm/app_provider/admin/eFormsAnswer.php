<?php


namespace App\eForm\app_provider\admin;


use App\core\controller\fieldService;
use App\core\controller\httpErrorHandler;
use App\eForm\model\eform;
use App\user\app_provider\api\user;
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


class eFormsAnswer extends \controller {
	public function index($formId = null){
		$this->lists($formId);
	}
	public function lists($formId = null , $userId = null) {
		$get = request::post('page=1,perEachPage=25,fname,lname,phone,email,StartTime,EndTime' ,null);
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
			if ( $get['fname'] != null ) {
				$value[] = '%'.$get['fname'].'%' ;
				$variable[] = ' u.fname LIKE ? ' ;
			}
			if ( $get['lname'] != null ) {
				$value[] = '%'.$get['lname'].'%' ;
				$variable[] = ' u.lname LIKE ? ' ;
			}
			if ( $get['email'] != null ) {
				$value[] = '%'.$get['email'].'%' ;
				$variable[] = ' u.email LIKE ? ' ;
			}
			if ( $get['phone'] != null ) {
				$value[] = '%'.$get['phone'].'%' ;
				$variable[] = ' u.phone LIKE ? ' ;
			}
			if ( $get['StartTime'] != null  and $get['EndTime'] == null ) {
				$value[] = $get['StartTime'] ;
				$variable[] = ' e.fillEnd >= ? ' ;
			} elseif ( $get['EndTime'] != null and $get['StartTime'] == null) {
				$value[] = $get['EndTime'] ;
				$variable[] = ' e.fillEnd <= ? ' ;
			} elseif ($get['EndTime'] != null and $get['StartTime'] != null) {
				$value[] = $get['StartTime'] ;
				$value[] = $get['EndTime'] ;
				$variable[] = ' ( e.fillEnd BETWEEN ? And ? ) ' ;
			}
		}
		if ( $formId != null ){
			$value[] = $formId ;
			$variable[] = ' e.formId = ? ' ;
		}
		if ( $userId != null ){
			$value[] = $userId ;
			$variable[] = ' e.userId = ? ' ;
		}
		/* @var eform $model */
		$model = parent::model('eformfilled');
		model::join('user u' , 'e.userId = u.userId', "left" );
		$numberOfAll = ($model->search( (array) $value  , ( count($variable) == 0 ) ? null : implode(' and ' , $variable) , 'eformfilled e', 'COUNT(e.fillId) as co' , null,null,'e.fillId')) [0]['co'];
		$pagination = parent::pagination($numberOfAll,$get['page'],$get['perEachPage']);
		model::join('user u' , 'e.userId = u.userId', "left" );
		model::join('eform form' , 'e.formId = form.formId', "left" );
		$search = $model->search( (array) $value  , ( ( count($variable) == 0 ) ? null : implode(' and ' , $variable) )  , 'eformfilled e', 'e.fillId,e.formId,form.name,u.lname,u.userId,u.fname,u.phone,u.email,e.fillEnd'  , ['column' => 'e.fillId' , 'type' =>'desc'] , [$pagination['start'] , $pagination['limit'] ] ,'e.fillId' );
		$this->mold->path('default', 'eForm');
		$this->mold->view('listOfFormAnswer.mold.html');
		$this->mold->setPageTitle(rlang(['answers' , 'eForm']));
		$this->mold->set('answers' , $search);
	}
	public function yourAnswer() {
		$userId = session::get('userAppLoginInformation')['userId'];
		if ( $userId > 0 ) {
			$this->lists(null, $userId);
			$this->mold->set('activeMenu','userAnswer');
		} else{
			httpErrorHandler::E403();
		}
	}
	public function answer($answerId){

		/* @var \App\eForm\model\eformfilled $answer */
		$answer = $this->model('eformfilled' , $answerId);
		if ( $answer->getFillId() == null ){
			$this->mold->offAutoCompile();
			\App\core\controller\httpErrorHandler::E404();
			return ;
		}
		$user = user::getUser($answer->getUserId(),' userId = ? ');
		/* @var \App\eForm\model\eform $answer */
		$form = $this->model('eform' , $answer->getFormId());

		$allFields = fieldService::showFilledOutFormWithAllFields($answer->getFormId(),'eForm',$answer->getFillId() , 'eformfilled');

//				$this->mold->offAutoCompile();
//				show($answer->returnAsArray(),false);
//				show($user->returnAsArray(),false);
//				show($form->returnAsArray());
		$this->mold->set('answer' , $answer);
		$this->mold->set('form' , $form);
		$this->mold->set('user' , $user);
		$this->mold->set('allFields' , $allFields['result']);
		$this->mold->path('default', 'eForm');
		$this->mold->view('answer.mold.html');
		$this->mold->setPageTitle(rlang('answer'));
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
}