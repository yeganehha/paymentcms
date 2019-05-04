<?php

/**
 * Project: pinoox
 * User: Esmaeil Bahrani Fard
 * Date: 7/8/2017
 * Time: 8:39 PM
 */
namespace paymentCms\component;


class session extends \SessionHandler
{
	private static $lifeTime = 60 * 60;
	private static $object = null ;
	private static $manualSession = false ;
	private static $gc_probability = 1 ;
	private static $gc_divisor = 100 ;

	/**
	 * @param int $gc_probability
	 *
	 * @return \paymentCms\component\session
	 */
	public static function setGcProbability($gc_probability) {
		self::$gc_probability = $gc_probability;
		ini_set('session.gc_probability', self::$gc_probability);
		return self::$object;
	}


	public function __init(){
		ini_set('session.cookie_httponly', 1);
		ini_set('session.use_only_cookies', 1);
		if (!empty(self::$lifeTime) and self::$lifeTime > 0) {
			session_set_cookie_params(self::$lifeTime);
			ini_set('session.cookie_lifetime', self::$lifeTime);
			ini_set('session.gc_maxlifetime', self::$lifeTime);
		}
		ini_set('session.gc_probability', self::$gc_probability);
		ini_set('session.gc_divisor', self::$gc_divisor);

		if (self::$manualSession) {
			// Set handler to override SESSION
			session_set_save_handler(
				array($this, "_open"),
				array($this, "_close"),
				array($this, "_read"),
				array($this, "_write"),
				array($this, "_destroy"),
				array($this, "_gc")
			);
		}
		if(session_id() == '')
			session_start();
		self::$object = new session();
	}

	/**
	 * @param bool $manualSession
	 *
	 * @return \paymentCms\component\session
	 */
	public static function setManualSession($manualSession) {
		self::$manualSession = $manualSession;
		return self::$object;
	}

	/**
	 * @param        $lifeTime
	 * @param string $type
	 *
	 * @return \paymentCms\component\session
	 */
	public static function lifeTime($lifeTime, $type = 'sec'){
		if ($type == 'min') $lifeTime = $lifeTime * 60;
		if ($type == 'hour') $lifeTime = $lifeTime * 60 * 60;
		if ($type == 'day') $lifeTime = $lifeTime * 60 * 60 * 24;
		self::$lifeTime = $lifeTime;
		session_set_cookie_params(self::$lifeTime);
		ini_set('session.cookie_lifetime', self::$lifeTime);
		ini_set('session.gc_maxlifetime', self::$lifeTime);
		return self::$object;
	}


	public static function set($variable , $value){
		$_SESSION[$variable] = $value ;
		return self::$object;
	}

	public static function get(){
		$args = func_get_args() ;
        if (func_num_args() > 1) {
	        $return = [] ;
	        foreach ($args as $arg)
		        if ( isset($_SESSION[$arg]) )
		        	$return[$arg] = $_SESSION[$arg];
		        else
			        $return[$arg] = null ;
            return $return ;
        } elseif (func_num_args() == 1) {
	        if ( isset($_SESSION[$args[0]]) )
		        return $_SESSION[$args[0]];
	        else
	        	return null ;
        } elseif (func_num_args() == 0) {
		        return $_SESSION;
        }
        return null ;
	}

	public static function has(){
		$args = func_get_args() ;
        if (func_num_args() > 1) {
	        $return = true ;
	        foreach ($args as $arg) {
		        if (!isset($_SESSION[$arg])) $return = false;
		        if ( ! $return) break;
	        }
            return $return ;
        } elseif (func_num_args() == 1) {
	        if ( isset($_SESSION[$args[0]]) )
		        return true;
	        else
	        	return false ;
        } else
        	return false;
	}

	public static function remove(){
		$args = func_get_args() ;
        if (func_num_args() > 1) {
	        foreach ($args as $arg)
		        if ( isset($_SESSION[$arg]) )
		        	unset($_SESSION[$arg]);
	        return self::$object;
        } elseif (func_num_args() == 1) {
	        if ( isset($_SESSION[$args[0]]) )
		        unset($_SESSION[$args[0]]);
	        return self::$object;
        } elseif (func_num_args() == 0) {
	        self::clear();
	        return self::$object;
        }
		return self::$object;
	}

	public static function getSessionId()
	{
		return session_id();
	}
	public static function regenerateSessionId($deleteOldSession = true)
	{
		session_regenerate_id($deleteOldSession);
		return self::$object;
	}

	public static function clear()
	{
		session_unset();
		session_destroy();
		return self::$object;
	}


	public static function stop()
	{
		session_write_close();
		session_unset();
		session_destroy();
		return self::$object;
	}

}