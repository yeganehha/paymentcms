<?php

namespace paymentCms\component ;
/**
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 3/24/2019
 * Time: 7:58 PM
 * project : paymentCMS
 * version : 0.0.0.1
 * update Time : 3/24/2019 - 7:58 PM
 * Description of this Page :
 */


if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


class request {

	public static function post( array $parameters){
		return self::check($_POST,$parameters);
	}

	public static function get( array $parameters){
		return self::check($_GET,$parameters , true);
	}

	public static function cookie( array $parameters){
		return self::check($_COOKIE,$parameters);
	}

	public static function server( array $parameters){
		return self::check($_SERVER,$parameters);
	}

	public static function all( array $parameters){
		return self::check($_REQUEST,$parameters);
	}

	public static function isGet($parameter){
		if ( isset($_GET[$parameter]))
			return true ;
		return false ;
	}
	public static function isPost($parameter){
		if ( isset($_COOKIE[$parameter]))
			return true ;
		return false ;
	}
	public static function isCookie($parameter){
		if ( isset($_POST[$parameter]))
			return true ;
		return false ;
	}
	public static function is($parameter){
		if ( isset($_REQUEST[$parameter]))
			return true ;
		return false ;
	}

	public static function getOne($parameter){
		if ( isset($_GET[$parameter]))
			return $_GET[$parameter] ;
		return false ;
	}

	public static function postOne($parameter){
		if ( isset($_POST[$parameter]))
			return $_POST[$parameter] ;
		return false ;
	}

	public static function cookieOne($parameter){
		if ( isset($_COOKIE[$parameter]))
			return $_COOKIE[$parameter] ;
		return false ;
	}

	public static function one($parameter){
		if ( isset($_REQUEST[$parameter]))
			return $_REQUEST[$parameter] ;
		return false ;
	}

	private static  function check($data , $parameters , $urlDecode = false ){
		if ( is_array($parameters) ){
			$return = array();
			foreach ( $parameters as $key => $defaultValue ){
				if ( is_int($key)) {
					if (isset($data[$defaultValue])) {
						if ($urlDecode)
							$return[$defaultValue] = urldecode($data[$defaultValue]);
						else
							$return[$defaultValue] = $data[$defaultValue];
					} else {
						$return[$defaultValue] = null ;
					}
				} else {
					if (isset($data[$key]))
						if ( $urlDecode )
							$return[$key] = urldecode($data[$key]);
						else
							$return[$key] = $data[$key];
					else {
						if (isset($defaultValue))
							$return[$key] = $defaultValue ;
					}
				}
			}
			return $return ;
		} else
			return array();
	}
}