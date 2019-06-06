<?php
namespace App\landing\controller ;

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

	/**
	 * @param null $id
	 * @param null $name
	 *                  [global-access]
	 */
	public function index($id = null , $name = null ){
		/* @var \App\landing\model\landingPage $page */
		if ( $id != null ){
			$page = $this->model('landingPage' , $id );
			if ( $page->getLandingPageId() != $id )
				\App\core\controller\httpErrorHandler::E404();
		} else {
			$page = $this->model('landingPage' , ' 1 ' , ' useAsDefault = ? ' );
			if ( $page->getLandingPageId() == null)
				\App\core\controller\httpErrorHandler::E404();
		}
		if ( is_file(__DIR__.'/../theme/default/'.$page->getTemplateName().'.content.mold.html') )
			$this->mold->view($page->getTemplateName().'.content.mold.html');
		else
			$this->mold->view('default.content.mold.html');
		$this->mold->setPageTitle($page->getName());
		$page->setTemplate(html_entity_decode($page->getTemplate()));
		$this->mold->set('page' , $page);
		$this->mold->unshow('footer.mold.html' ,'header.mold.html');
	}
}