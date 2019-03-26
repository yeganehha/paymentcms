<?php

namespace paymentCms\component\mold;

use paymentCms\component\request;

/**
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 3/23/2019
 * Time: 12:48 PM
 * project : paymentCms
 * virsion : 0.0.0.1
 * update Time : 3/23/2019 - 12:48 PM
 * Discription of this Page :
 */
class Mold {

	private  $mold ;
	private $moldData ;
	private $moldFiles ;
	public $autoCompile = true ;

	public function __construct() {
		$this->init($this);
	}

	/**
	 * when you want to call it as builder pattern use init
	 * @link https://pinoox.com/documnet/mold/methods/init
	 * @return Mold
	 * @example ->init();
	 */
	public function init($mold = null) {
		$this->moldData = new MoldData();
		$this->moldFiles = new MoldFiles($this->moldData);
		if ( is_null($mold) ) {
			$this->mold = new Mold();
		} else {
			$this->mold = $mold;
		}
		if ( Request::isGet('moldAssetsFileLoader') ){
			$this->autoCompile = false ;
			$this->moldFiles->renderAssets(Request::getOne('moldAssetsFileLoader'));
		}
		return $this->mold;

	}

	/**
	 * this method get variable to set in template and render it when template is compiling
	 * @link https://pinoox.com/documnet/mold/methods/set
	 * @param string : name of variable that call it from front
	 * @param mixed : value that return when variable call from front
	 * @param array : 2D array that index of array is variable that call from front and value of array return on call variable in front
	 * @return Mold
	 * @example : ->set('username' , 'admin');
	 * @example : ->set( array( 'username' => 'admin' ) );
	 * @example : ->set( 'username' => $object ) );
	 * @example : ->set( 'user' => array( 'username' => 'admin' , 'userAvatar' => 'avatar.png' ) ) );
	 */
	public function set(){
		call_user_func_array(array($this->moldData , 'set') , func_get_args() );
		return $this->mold ;
	}

	/**
	 * set page title in header of html
	 * @link https://pinoox.com/documnet/mold/methods/setPageTitle
	 * @param string : title of current page
	 * @return Mold
	 * @example ->setPageTitle('hello world!');
	 */
	public function setPageTitle($pageTitle){
		call_user_func_array(array($this->moldData , 'set') , ['_title' , $pageTitle] );
		return $this->mold ;
	}

	/**
	 * this method get user information to set in template and render it when template is compiling
	 * @link https://pinoox.com/documnet/mold/methods/setUser
	 * @param mixed : value that return when <_user> call from front
	 * @return Mold
	 * @example ->setPageTitle($user_object);
	 * @example ->setPageTitle($user_array);
	 */
	public function setUser($user){
		call_user_func_array(array($this->moldData , 'set') , ['_user' , $user] );
		return $this->mold ;
	}

	/**
	 * this method get config values to set in template and render it when template is compiling
	 * @link https://pinoox.com/documnet/pintemplate/methods/setConfig
	 * @param mixed : value that return when <_config> call from front
	 * @return Mold
	 * @example ->setConfig($config_object);
	 * @example ->setConfig($config_array);
	 */
	public function setConfig($config){
		call_user_func_array(array($this->moldData , 'set') , ['_config' , $config] );
		return $this->mold ;
	}

	/**
	 * this method get variable to unset that
	 * @link https://pinoox.com/documnet/mold/methods/remove
	 * @param string : name of variable that call it from front
	 * @return Mold
	 * @example ->remove('variable');
	 * @example ->remove('variable','other_variable');
	 */
	public function remove(){
		call_user_func_array(array($this->moldData , 'remove') , func_get_args() );
		return $this->mold ;
	}

	/**
	 * this method get variable to check set before and return value of that or not set yet
	 * @link https://pinoox.com/documnet/mold/methods/get
	 * @param string : name of variable that needed value
	 * @return mixed :
	 * @example ->get('variable');
	 * @example ->get('variable','other_variable');
	 * @example ->get();
	 */
	public function get(){
		return call_user_func_array(array($this->moldData , 'get') , func_get_args() );
	}

	/**
	 * compile and show html page after all controller finish processes
	 * on default : auto compiled
	 * @link https://pinoox.com/documnet/mold/methods/AutoCompile
	 * @return Mold :
	 * @example ->AutoCompile();
	 */
	public function AutoCompile(){
		$this->autoCompile = true ;
		return $this->mold ;
	}


	/**
	 * turn off compile and show html page after all controller finish processes
	 * @link https://pinoox.com/documnet/mold/methods/offAutoCompile
	 * @return Mold :
	 * @example ->offAutoCompile();
	 */
	public function offAutoCompile(){
		$this->autoCompile = false ;
		return $this->mold ;
	}

	/**
	 * change path of file to be read and then compile
	 * @link https://pinoox.com/documnet/mold/methods/path
	 * @param string $folder optional: name of folder in app>theme . on null , run template files from app>theme.
	 * @param string $app optional : name of app [default : current app]
	 * @return Mold :
	 * @example ->path('default');
	 * @example ->path();
	 * @example ->path('default' , 'com_pinoox_front' );
	 */
	public function path($folder = null ,$app = null){
		$this->moldFiles->setPath($folder , $app);
		return $this->mold ;
	}

	/**
	 * push file in to the list of file should compile after header and before footer
	 * @link https://pinoox.com/documnet/mold/methods/view
	 * @param string : name of file that should compile
	 * @return Mold :
	 * @example ->view('login.mold.html');
	 * @example ->view('person>login.mold.html' );
	 * @example ->view('person/login.mold.html' );
	 * @example ->view('person\login.mold.html' );
	 * @example ->view('sidebar.mold.html' , 'login.mold.html' );
	 * @example ->view('panel\sidebar.mold.html' , 'person>login.mold.html' );
	 */
	public function view(){
		$files = func_get_args() ;
		array_unshift( $files , 'body' )   ;
		call_user_func_array(array($this->moldFiles , 'appendView') , $files  );
		return $this->mold ;
	}


	/**
	 * push file in to the list of file should compile as a header (top off other file).
	 * @link https://pinoox.com/documnet/mold/methods/header
	 * @param string : name of file that should compile
	 * @return Mold :
	 * @example ->header('header.mold.html');
	 * @example ->header('person>header.mold.html' );
	 * @example ->header('person/header.mold.html' );
	 * @example ->header('person\header.mold.html' );
	 * @example ->header('header.mold.html' , 'sidebar.mold.html' );
	 * @example ->header('panel\header.mold.html' , 'person>sidebar.mold.html' );
	 */
	public function header(){
		$files = func_get_args() ;
		array_unshift( $files , 'header' )   ;
		call_user_func_array(array($this->moldFiles , 'appendView') , $files  );
		return $this->mold ;
	}

	/**
	 * push file in to the list of file should compile as a footer (end off other file).
	 * @link https://pinoox.com/documnet/mold/methods/footer
	 * @param string : name of file that should compile
	 * @return Mold :
	 * @example ->header('footer.mold.html');
	 * @example ->header('person>footer.mold.html' );
	 * @example ->header('person/footer.mold.html' );
	 * @example ->header('person\footer.mold.html' );
	 * @example ->header('copyright.mold.html' , 'footer.mold.html' );
	 * @example ->header('panel\copyright.mold.html' , 'person>footer.mold.html' );
	 */
	public function footer(){
		$files = func_get_args() ;
		array_unshift( $files , 'footer' )   ;
		call_user_func_array(array($this->moldFiles , 'appendView') , $files  );
		return $this->mold ;
	}


	/**
	 * push file in to the list of file after special file.
	 * @link https://pinoox.com/documnet/mold/methods/after
	 * @param string : name of file should find in list and insert other file after that
	 * @param string : list of file that should insert after special file
	 * @return Mold :
	 * @example ->after('body.mold.html' ,'footer.mold.html');
	 * @example ->after('person>body.mold.html' , 'footer.mold.html' );
	 * @example ->after('person/body.mold.html' , 'footer.mold.html' );
	 * @example ->after('person\body.mold.html' , 'footer.mold.html' );
	 * @example ->after('person/body.mold.html' , 'copyright.mold.html' , 'footer.mold.html' );
	 * @example ->after('body.mold.html' , 'panel\copyright.mold.html' , 'panel>footer.mold.html' );
	 */
	public function after(){
		if ( func_num_args() > 1  ){
			call_user_func_array(array($this->moldFiles , 'addViewAfter') , func_get_args()   );
			return $this->mold ;
		} else {
			// TODO : add Exception
			return $this->mold ;
		}
	}

	/**
	 * push file in to the list of file before special file.
	 * @link https://pinoox.com/documnet/mold/methods/before
	 * @param string : name of file should find in list and insert other file before that
	 * @param string : list of file that should insert before special file
	 * @return Mold :
	 * @example ->before('footer.mold.html' , 'body.mold.html');
	 * @example ->before('footer.mold.html' , 'person>body.mold.html' );
	 * @example ->before('footer.mold.html' , 'person/body.mold.html' );
	 * @example ->before('footer.mold.html' , 'person\body.mold.html'  );
	 * @example ->before('footer.mold.html' , 'person/body.mold.html' , 'copyright.mold.html' );
	 * @example ->before('panel>footer.mold.html' , 'body.mold.html' , 'panel\copyright.mold.html' );
	 */
	public function before(){
		if ( func_num_args() > 1  ){
			call_user_func_array(array($this->moldFiles , 'addViewBefore') , func_get_args()   );
			return $this->mold ;
		} else {
			// TODO : add Exception
			return $this->mold ;
		}
	}

	/**
	 * this method get files to dont show view
	 * @link https://pinoox.com/documnet/mold/methods/unshow
	 * @param string : name of files that should not show
	 * @return Mold
	 * @example ->unshow('copyright.mold.html');
	 * @example ->unshow('copyright.mold.html','footer.mold.html');
	 */
	public function unshow(){
		call_user_func_array(array($this->moldFiles , 'unshow') , func_get_args() );
		return $this->mold ;
	}

	/**
	 * return list of file should render order by index
	 * @link https://pinoox.com/documnet/mold/methods/getViews
	 * @return array :
	 * @example ->getViews();
	 */
	public function getViews(){
		return $this->moldFiles->listView();
	}


	/**
	 * render template and return html code
	 * @link https://pinoox.com/documnet/mold/methods/render
	 * @return string :
	 * @example ->render();
	 */
	public function render(){
		return $this->moldFiles->render();
	}

	/**
	 * this method cause cache file minify. default do not minify file
	 * @link https://pinoox.com/documnet/mold/methods/minifyCache
	 * @return Mold
	 * @example ->minifyCache();
	 */
	public function minifyCache(){
		$this->moldFiles->setMinifyHtml(true);
		return $this->mold ;
	}

	/**
	 * this method dont allow minify cache file
	 * @link https://pinoox.com/documnet/mold/methods/offMinifyCache
	 * @return Mold
	 * @example ->offMinifyCache();
	 */
	public function offMinifyCache(){
		$this->moldFiles->setMinifyHtml(false);
		return $this->mold ;
	}

	/**
	 * enable or disable cache compiled template file or on enable it, set cache life time.
	 * if don't call this method any time, cache system turn off
	 * @link https://pinoox.com/documnet/mold/methods/cache
	 * @param boolean|int $time [optional]:
	 *                          if do not send any params to this method, cache turn on and cache life time set 5 days
	 *                          if send true, cache turn on and cache life time set 5 days
	 *                          if send false, cache turn off
	 *                          if send integer( per second), cache turn on and cache life time set to your value .
	 * @return Mold
	 * @example ->cache(true);
	 * @example ->cache();
	 * @example ->cache(false);
	 * @example ->cache(3600);
	 */
	public function cache($time = 432000){
		if ( $time === false )
			$time = false ;
		elseif ( $time === null or empty($time))
			$time = 0 ;
		elseif ( intval($time) > -1  )
			$time = intval($time) ;
		else
			$time = 432000 ;
		$this->moldFiles->setCacheLifeTime($time);
		return $this->mold ;
	}


	public function __destruct() {
		if ( $this->autoCompile )
			echo self::render();
	}
}