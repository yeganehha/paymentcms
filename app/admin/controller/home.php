<?php


namespace App\admin\controller;


/**
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 6/6/2019
 * Time: 6:38 PM
 * project : paymentCMS
 * virsion : 0.0.0.1
 * update Time : 6/6/2019 - 6:38 PM
 * Discription of this Page :
 */


if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


class home extends \controller {


	public function index(){
		$appsApiLinks ="http://localhost/payment/api/apps/updateWithNews";
		$localData = array(
			'version' => PCVERSION ,
			'siteUrl' => \App::getBaseAppLink(null,'core'),
			'lang' => 'fa',
			'theme' => 'default',
			'app' => json_encode(\App::appsListWithConfig()),
		);
		$update = json_decode(curl($appsApiLinks,$localData),true);
		if ( $update['update']['needUpdate'])
			$this->alert('danger' , rlang('update') ,rlang('needUpdate') , 'autorenew');

		$this->mold->set('tabOne' , $update['panel1']);
		$this->mold->set('tabTwo' , $update['panel2']);
		$this->mold->view('home.mold.html');
		$this->mold->setPageTitle(rlang('dashboard'));
		$this->mold->set('activeMenu' , 'dashboard');
	}
}