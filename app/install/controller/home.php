<?php
namespace App\install\controller;

use paymentCms\component\file;
use paymentCms\component\model;
use paymentCms\component\mold\Mold;
use paymentCms\component\request;
use paymentCms\component\Response;
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
		if ( request::isGet('refresh') ){
			session::set('installInfo',null);
			Response::redirect(\App::getCurrentBaseLink());
			exit;
		}
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

	/**
	 *
	 */
	public static function step3(){
		self::__init();
		if ( request::isPost('step3') ){
			$listApps = include __DIR__.DIRECTORY_SEPARATOR.'listOfAppShouldInstall.php';
			$db = session::get('installInfo')['databaseConnectionInfo'] ;
			$user = session::get('installInfo')['userInfo'] ;
			self::$db = new \database($db['host'], $db['user'], $db['pass'], $db['name']);
			self::$db->setPrefix($db['prefix']);
			model::setDb(self::$db);
			model::transaction();
			$allResultNotSave = false ;
			$appStatus = [];
			if ( is_array($listApps ) ){
				plugins::setPrefix($db['prefix']);
				foreach ($listApps as $app ){
					$result = plugins::installLocal($app);
					if ( $result == false ){
						$allResultNotSave[] = $app ;
					} else
						$appStatus[$app] = 'active';
				}
				plugins::changeCacheOfAppStatus($appStatus);
			}
			if ( $appStatus['core'] == 'active'){
				$model = new  \paymentCms\model\apps_link();
				$model->setLink( \App::getCurrentBaseLink().'admin');
				$model->setApp('admin');
				$model->insertToDataBase();
				$model->setLink( \App::getCurrentBaseLink().'users');
				$model->setApp('user');
				$model->insertToDataBase();
			}
			if ( $appStatus['api'] == 'active'){
				$model = new  \paymentCms\model\api();
				$model->setDomain( ( $_SERVER['HTTPS'] == 'on' ? 'https://':'http://' ).$_SERVER['HTTP_HOST'] );
				$model->setName(rlang('Internal'));
				$model->setAllowIp('localhost');
				$model->setActive( 1);
				$model->insertToDataBase();
			}
			if ( $appStatus['user'] == 'active'){
				$model = new  \App\user\model\user_group();
				$model->setLoginRequired( 1);
				$model->setName( rlang('Administer'));
				$adminGroupId = $model->insertToDataBase();
				$model->setLoginRequired( 0);
				$model->setName( rlang('Guest'));
				$model->insertToDataBase();
				if ( $adminGroupId !== false){
					$model = new  \App\user\model\user_group_permission();
					$model->setUserGroupId( $adminGroupId);
					$model->setAccessPage( '--FULL-ACCESS--');
					$model->insertToDataBase();
					$model = new \paymentCms\model\user();
					$model->setUserGroupId($adminGroupId);
					$model->setFname($user['fname']);
					$model->setLname($user['lname']);
					$model->setEmail($user['email']);
					$model->setPhone($user['phone']);
					$model->setPassword($user['pass']);
					$model->setBlock(0);
					$model->setAdminNote('');
					$model->setRegisterTime( date('Y-m-d H:i:s') );
					$resultInsertUser = $model->insertToDataBase();
				}
			}
			self::creatConfigFile();
			self::$mold->view('step4.mold.html');
			self::$mold->set('user',session::get('installInfo')['userInfo']);
			self::$mold->set('adminLink',\App::getCurrentBaseLink().'admin');
			self::$mold->offAutoCompile();
			echo self::$mold->render();
			file::removedir( payment_path.'app'.DIRECTORY_SEPARATOR.'install');
		} else {
			self::checkPhp();
			self::$mold->view('step3.mold.html');
			self::$mold->set('user',session::get('installInfo')['userInfo']);
			self::$mold->set('db',session::get('installInfo')['databaseConnectionInfo']);
			self::$mold->set('link',\App::getCurrentBaseLink());
		}
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

	private static function creatConfigFile() {
		$db = session::get('installInfo')['databaseConnectionInfo'] ;
		try {
			$php = '';
			$php .= "<?php\n";
			$php .= "\t/*  created at : " . date('Y-m-d H:i:s') . "*/ \n";
			$php .= "\n\n";
			$php .= "return ['_dbHost' => '".$db['host']."' , '_dbUsername' => '".$db['user']."' , '_dbPassword' => '".$db['pass']."' , '_dbName' => '".$db['name']."' , '_dbTableStartWith' => '".$db['prefix']."'] ; \n";
			File::generate_file(payment_path.'core'.DIRECTORY_SEPARATOR.'config.php', $php);
			return true;
		} catch (\Exception $exception){
			return false ;
		}
	}

	public function __destruct() {
		if ( ! is_null(self::$alert) and ! is_null(self::$alert))
			self::$mold->set('alert' , self::$alert );
	}

	private static function checkPhp(){
		$listFunction = include __DIR__.DIRECTORY_SEPARATOR.'listOfFunction.php';
		$listExtension = include __DIR__.DIRECTORY_SEPARATOR.'listOfExtension.php';
		$insertToMold = [] ;
		$allHas = true ;
		if (version_compare(phpversion(), "4.3.0", ">=")) {
			$insertToMold[] = ['name' => 'php version: '.phpversion(), 'status' => true];
		} else {
			$insertToMold[] = ['name' => 'php version: '.phpversion(), 'status' => false];
			$allHas = false;
		}
		if ( is_array($listFunction) ){
			foreach ( $listFunction as $key => $value ){
				if ( function_exists($value) )
					$insertToMold[] = ['name' => $value , 'status' => true ];
				else {
					$insertToMold[] = ['name' => $value, 'status' => false];
					$allHas = false;
				}
			}
		}
		if ( is_array($listExtension) ){
			foreach ( $listExtension as $key => $value ){
				if ( extension_loaded($value) )
					$insertToMold[] = ['name' => $value , 'status' => true ];
				else {
					$insertToMold[] = ['name' => $value, 'status' => false];
					$allHas = false;
				}
			}
		}

		self::$mold->set('function',$insertToMold);
		self::$mold->set('functionSafe',$allHas);
		return $allHas ;

	}
}