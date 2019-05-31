<?php
namespace App\install\controller;

use paymentCms\component\mold\Mold;
use paymentCms\component\request;
use paymentCms\component\session;
use paymentCms\component\validate;

/**
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 5/30/2019
 * Time: 10:35 PM
 * project : paymentCMS
 * virsion : 0.0.0.1
 * update Time : 5/30/2019 - 10:35 PM
 * Discription of this Page :
 */


if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


class home {
	private static  $mold ;
	private static  $db ;
	private static  $alert ;
	public static function __init(){
		self::$mold = new Mold();
		self::$mold ->set('float' , 'right');
		self::$mold ->set('text_align' , 'right');
		self::$mold ->path('default' ,'install');
		self::$mold ->unshow('footer.mold.html' ,'header.mold.html');
	}
	public static function index() {
		if ( ! session::has('installInfo') )
			self::step1();
		else {
			switch ( session::get('installInfo')['step'] ){
				case 'step2' :
					self::step2();
					break;
				case 'step3' :
					self::step3();
					break;
				default :
					self::step1();
					break;
			}
		}
	}

	public static function step1(){
		self::__init();
		if ( request::isPost('step1') ){
			$form = request::post('host=localhost,name,user=root,pass,prefix');
			self::$db = new \database($form['host'], $form['user'], $form['pass'], $form['name']);
			try {
				$result = self::$db->connect();
				session::set('installInfo' , ['step' => 'step2' , 'databaseConnectionInfo' => $form]);
				$_POST=null;
				self::step2();
			} catch (\Exception $e) {
				self::alert('danger' , null,rlang('databaseInformationIsWrong'));
				self::$mold ->view('step1.mold.html');
			}
		} else
			self::$mold ->view('step1.mold.html');
	}

	public static function step2(){
		self::__init();
		if ( request::isPost('step2') ){
			$form = request::post('fname,lname,email,pass,phone,groupId=1,block=0');
			$rules = [
				"fname" => ["required", rlang('firstName')],
				"lname" => ["required", rlang('lastName')],
				"groupId" => ["required|match:>0", rlang('permission')],
				"email" => ["required|email", rlang('email')],
				"phone" => ["required|mobile", rlang('phone')],
				"block" => ["required|format:{0/1}", rlang('block')],
				"pass" => ["required", rlang('password')]
			];
			$valid = validate::check($form, $rules);
			if ($valid->isFail()){
				self::alert('danger' , null,$valid->errorsIn());
				self::$mold ->view('step2.mold.html');
			} else {
				$installInfo = session::get('installInfo');
				$installInfo['step'] = 'step3';
				$installInfo['userInfo'] = $form;
				session::set('installInfo' , $installInfo);
				self::step3();
			}
		} else
			self::$mold ->view('step2.mold.html');
	}

	public static function step3(){
		self::__init();
		if ( request::isPost('step3') ){
			$form = request::post('fname,lname,email,pass,phone,groupId=1,block=0');
			$rules = [
				"fname" => ["required", rlang('firstName')],
				"lname" => ["required", rlang('lastName')],
				"groupId" => ["required|match:>0", rlang('permission')],
				"email" => ["required|email", rlang('email')],
				"phone" => ["required|mobile", rlang('phone')],
				"block" => ["required|format:{0/1}", rlang('block')],
				"pass" => ["required", rlang('password')]
			];
			$valid = validate::check($form, $rules);
			if ($valid->isFail()){
				self::alert('danger' , null,$valid->errorsIn());
				self::$mold ->view('step2.mold.html');
			} else {
				$installInfo = session::get('installInfo');
				$installInfo['step'] = 'step3';
				$installInfo['userInfo'] = $form;
				session::set('installInfo' , $installInfo);
				self::step4();
			}
		} else
			self::$mold ->view('step2.mold.html');
	}

	private static function alert($type , $title , $description ,$icon = null , $close = true ){
		if ( $icon != null )
			$temp['icon'] = $icon ;
		if ( $title != null )
			$temp['title'] = $title ;
		$temp['description'] = $description ;
		$temp['type'] = $type ;
		$temp['canClose'] = $close ;
		self::$alert[] = $temp;
	}
	public function __destruct() {
		if ( ! is_null(self::$alert) and ! is_null(self::$alert))
			self::$mold->set('alert' , self::$alert );
	}
}