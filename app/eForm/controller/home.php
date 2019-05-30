<?php
namespace App\eForm\controller ;

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

		if ( is_file(__DIR__.'/../theme/default/'.$form->getTemplateName().'.form.mold.html') )
			$this->mold->view($form->getTemplateName().'.form.mold.html');
		else
			$this->mold->view('default.form.mold.html');
		$this->mold->setPageTitle($form->getName());
		$form->setDescription(html_entity_decode($form->getDescription()));
		$this->mold->set('form' , $form);
		$this->mold->unshow('footer.mold.html' ,'header.mold.html');
	}
}