<?php
namespace App\eForm\controller ;

use App\core\controller\fieldService;
use paymentCms\component\model;
use paymentCms\component\request;
use paymentCms\component\security;
use paymentCms\component\session;
use paymentCms\component\strings;

/**
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 5/29/2019
 * Time: 2:15 PM
 * project : paymentCMS
 * virsion : 0.0.0.1
 * update Time : 5/29/2019 - 2:15 PM
 * Discription of this Page :
 */


if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


class home extends \controller {

	public function index($id = null , $name = null ){
		if ( ! session::has('userAppLoginInformation') ) {
			\App\core\controller\httpErrorHandler::E403();
		}

		$user = session::get('userAppLoginInformation');

		/* @var \App\eForm\model\eform $form */
		if ( $id != null ){
			$form = $this->model('eform' , $id );
			if ( $form->getFormId() != $id )
				\App\core\controller\httpErrorHandler::E404();
		} else {
			\App\core\controller\httpErrorHandler::E404();
		}

		if ( ! $form->getPublished() )
			\App\core\controller\httpErrorHandler::E404();

		if ( ! strings::strhas($form->getAccess() , ','.$user['user_group_id'].','))
			\App\core\controller\httpErrorHandler::E403();

		if ( $form->getOneTime() ){
			/* @var \App\eForm\model\eformfilled $fill */
			$fill = $this->model('eformfilled' , [ $form->getFormId() ,$user['userId'] ] , ' formId = ? and userId = ?' );
			if ( $fill->getFillId() > 0 )
				\App\core\controller\httpErrorHandler::E403();
		}

		if ( request::isPost() ) {
			$this->checkData($form);
		}
		if ( is_file(__DIR__.'/../theme/default/'.$form->getTemplateName().'.form.mold.html') )
			$this->mold->view($form->getTemplateName().'.form.mold.html');
		else
			$this->mold->view('default.form.mold.html');
		$this->mold->setPageTitle($form->getName());
		$form->setDescription(html_entity_decode($form->getDescription()));
		$form->setLastNote(html_entity_decode($form->getLastNote()));
		$this->mold->set('form' , $form);
		$this->mold->unshow('footer.mold.html' ,'header.mold.html');
	}

	private function checkData(&$form){
		/* @var \App\eForm\model\eform $form */
		$step = request::postOne('step');
		if ( $step == 'start' ){
			session::set('timeStartFillForm_'.$form->getFormId() , date('Y-m-d H:i:s'));
			fieldService::getFieldsToFillOut($form->getFormId(),'eForm' ,$this->mold);
			$this->mold->set('showField',true);
		} elseif ( $step == 'finish' ){
			model::transaction();
			$error = false;
			/* @var \App\eForm\model\eformfilled $fill */
			$fill = $this->model('eformfilled' );
			$fill->setUserId( session::get('userAppLoginInformation')['userId'] );
			$fill->setFormId($form->getFormId());
			$fill->setFillStart(session::get( 'timeStartFillForm_'.$form->getFormId()  ));
			$fill->setFillEnd(date('Y-m-d H:i:s'));
			$fill->setIp(security::getIp());
			$result = $fill->insertToDataBase();
			if ( $fill->getFillId() > 0 ){
				$customField = request::postOne('customField');
				$resultFillOutForm = fieldService::fillOutForm($form->getFormId(),'eForm',$customField, $fill->getFillId() , 'eformfilled');
				if ( ! $resultFillOutForm['status'] )
					$error = $resultFillOutForm['massage'];
			} else
				$error = rlang('pleaseTryAGain');
			if ( $error === false ){
				model::commit();
				$this->alert('success','',rlang('insertSuccessfully') .' '.$fill->getIp());
				$this->mold->set('showDone',true);
			} else {
				model::rollback();
				$this->alert('danger','',$error);
				fieldService::getFieldsToFillOut($form->getFormId(),'eForm' ,$this->mold);
				$this->mold->set('showField',true);
			}
		}
	}
}