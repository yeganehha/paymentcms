<?php
/**
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 3/23/2019
 * Time: 2:41 PM
 * project : paymentCMS
 * virsion : 0.0.0.1
 * update Time : 3/23/2019 - 2:41 PM
 * Discription of this Page :
 */


use paymentCms\component\file;

if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


function lang($langs , $implode = ' ',$lang = 'fa' ){
	echo implode($implode , (array) returnLang($langs , $lang ));
}

function rlang($langs , $implode = ' ',$lang = 'fa' ){
	return implode($implode , (array) returnLang($langs , $lang ));
}

function returnLang($langs,$lang = 'fa'){
	return Lang::init()->setLang($lang)->get($langs);
}

class Lang {
	private static $langC ;
	private static $fileInclude ;
	private static $lang = [];
	private static $langToShow = 'fa';
	private static $loadedFile = [];

	public function __construct() {
		$this->init($this);
	}

	public static function init($langC = null){
		$core_lang_path = payment_path.'core'.DIRECTORY_SEPARATOR.'lang'.DIRECTORY_SEPARATOR.self::$langToShow.'.php' ;
		self::loadLangFile($core_lang_path);
		$files = file::get_files_by_pattern(payment_path.'plugins'.DIRECTORY_SEPARATOR,'*'.DIRECTORY_SEPARATOR.'lang'.DIRECTORY_SEPARATOR.self::$langToShow.'.php');
		foreach ($files as $file) {
			self::loadLangFile($file);
		}
		if ( is_null($langC) ) {
			self::$langC = new Lang();
		} else {
			self::$langC = $langC;
		}
		return self::$langC ;
	}

	/**
	 * @return string
	 */
	public static function getLang() {
		return self::$langToShow;
	}

	/**
	 * @param string $langToShow
	 */
	public static function setLang($langToShow) {
		self::$langToShow = $langToShow;
		return self::$langC ;
	}

	private static function loadLangFile($file) {
		if (file_exists($file) and !in_array($file, self::$loadedFile)) {
			self::$loadedFile[] = $file;
			$newLang = require_once  $file  ;
			self::$lang = array_merge(self::$lang,$newLang);
//			self::$lang = array_unique(self::$lang);
		}
		return null;
	}

	public static function addToLangfile($app){
		$app_lang_path = \app::getAppPath('lang/' , $app).self::$langToShow.'.php' ;
		self::loadLangFile($app_lang_path);
	}

	public static function get(){
//		$app_lang_path = \app::getAppPath('lang/').self::$langToShow.'.php' ;
//		self::loadLangFile($app_lang_path);
		$Want = func_get_args() ;
		$return = [] ;
		if ( func_num_args() > 1 ) {
			for ($i = 0; $i < func_num_args(); $i++)
				if (isset(self::$lang[$Want[$i]]))
					$return[$Want[$i]] = self::$lang[$Want[$i]];
				else
					$return[$Want[$i]] = null;
		} elseif ( func_num_args() == 1  ) {
			if ( is_array($Want[0]) and count($Want[0]) > 1 ) {
				for ($i = 0; $i < count($Want[0]); $i++)
					if (isset(self::$lang[$Want[0][$i]]))
						$return[$Want[0][$i]] = self::$lang[$Want[0][$i]];
					else
						$return[$Want[0][$i]] = null;
			} elseif ( is_array($Want[0]) and count($Want[0]) == 1 ){
				return $return[$Want[0][0]] ;
			} elseif ( ! is_array($Want[0]) ) {
				return self::$lang[$Want[0]];
			}
		}
		return $return ;
	}
}