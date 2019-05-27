<?php


namespace paymentCms\component;


/**
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 4/18/2019
 * Time: 2:13 PM
 * project : paymentCMS
 * virsion : 0.0.0.1
 * update Time : 4/18/2019 - 2:13 PM
 * Discription of this Page :
 */


if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


class security {
	private static $method_openssl_encrypt = 'AES-128-CBC';
	private static $method_mcrypt_encrypt = 'MCRYPT_RIJNDAEL_128';
	public static function encrypt($string,$type = 'openssl' ,$canDecrypt = false ){
		if ( $canDecrypt ){
			if ( $type ==  'openssl' and extension_loaded('openssl') ){
				return self::openssl_encrypt($string);
			} elseif ( $type ==  'mcrypt' and extension_loaded('mcrypt')  ){
				return self::mcrypt_encrypt($string);
			} elseif ( $type ==  'base64' and function_exists('base64_encode')  ){
				return self::base64_encrypt($string);
			}
			if ( extension_loaded('openssl')  ){
				return self::openssl_encrypt($string);
			} elseif ( extension_loaded('mcrypt' ) ){
				return self::mcrypt_encrypt($string);
			} elseif ( function_exists('base64_encode') ) {
				return self::base64_decode($string ) ;
			}
		} else
			return self::md5( $string  );
		return false ;
	}

	public static function decrypt($string,$type = 'openssl'){
		if ( $type ==  'openssl' and extension_loaded('openssl') ){
			return self::openssl_decrypt($string);
		} elseif ( $type ==  'mcrypt' and extension_loaded('mcrypt')  ){
			return self::mcrypt_decrypt($string);
		} elseif ( $type ==  'base64'  ){
			return self::base64_decode($string);
		}
		if ( extension_loaded('openssl') ){
			return self::openssl_decrypt($string);
		} elseif ( extension_loaded('mcrypt' ) ){
			return self::mcrypt_decrypt($string);
		} elseif ( function_exists('base64_encode') ){
			return self::base64_decode($string ) ;
		}
		return false ;
	}

	private static function slat(){
		if ( ! cache::hasLifeTime('securitySlat','paymentCms')) {
			$slat = strings::generateRandomString();
			cache::save($slat, 'securitySlat', PHP_INT_MAX , 'paymentCms');
		} else {
			$slat = cache::get('securitySlat',null,'paymentCms');
		}
		return $slat ;
	}

	private static function openssl_encrypt($string){
		$ivlen = openssl_cipher_iv_length(self::$method_openssl_encrypt);
		$iv = openssl_random_pseudo_bytes($ivlen);
		$ciphertext_raw = openssl_encrypt($string, self::$method_openssl_encrypt, self::slat(), $options=OPENSSL_RAW_DATA, $iv);
		return str_replace(['/','='] , ['~',''],base64_encode( $iv.$ciphertext_raw ));
	}
	private static function openssl_decrypt($string){
		$c = base64_decode(str_replace('~','/',$string));
		$ivlen = openssl_cipher_iv_length(self::$method_openssl_encrypt);
		$iv = substr($c, 0, $ivlen);
		$ciphertext_raw = substr($c, $ivlen);
		return openssl_decrypt($ciphertext_raw, self::$method_openssl_encrypt, self::slat(), $options=OPENSSL_RAW_DATA, $iv);
	}

	private static function mcrypt_encrypt($string){
		return mcrypt_encrypt(self::$method_mcrypt_encrypt , self::slat(),$string, MCRYPT_MODE_CBC);
	}
	private static function mcrypt_decrypt($string){
		return mcrypt_decrypt(self::$method_mcrypt_encrypt ,self::slat(),$string, MCRYPT_MODE_CBC);
	}

	private static function base64_encrypt($string){
		return base64_encode($string.self::slat() ) ;
	}
	private static function base64_decode($string){
		return strings::deleteWordLastString(base64_decode($string ),self::slat()) ;
	}

	private static function md5($string){
		return md5( $string . self::slat() );
	}


	/**
	 * @return string
	 */
	public static function getIp(){
		if (isset($_SERVER['HTTP_CLIENT_IP']))
			$ipAddress = $_SERVER['HTTP_CLIENT_IP'];
		else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_X_FORWARDED']))
			$ipAddress = $_SERVER['HTTP_X_FORWARDED'];
		else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
			$ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_FORWARDED']))
			$ipAddress = $_SERVER['HTTP_FORWARDED'];
		else if(isset($_SERVER['REMOTE_ADDR']))
			$ipAddress = $_SERVER['REMOTE_ADDR'];
		else
			$ipAddress = 'UNKNOWN';
		return $ipAddress;
	}
}