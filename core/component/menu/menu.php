<?php


namespace paymentCms\component\menu;


/**
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 3/25/2019
 * Time: 11:14 AM
 * project : paymentCMS
 * virsion : 0.0.0.1
 * update Time : 3/25/2019 - 11:14 AM
 * Discription of this Page :
 */


if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


class menu {

	private static $menu = [] ;
	private static $menuKeys = [];
	private static $menuFatherKeys = [];
	private $menuTitle = [];
	public function __construct($title) {
		$this->menuTitle = $title ;
	}

	/**
	 * @param $title
	 *
	 * @return array
	 */
	public function getMenu($title) {
		return self::$menu[$title];
	}

	public function add($key , $title,$link = '#',$icon = null,$target = '' , $fatherKey = null){
		if ( $fatherKey == null )
			$fatherKey = $this->menuTitle ;
		else
			$fatherKey = $this->generateChildName($fatherKey);

		if ( isset(self::$menuKeys[$fatherKey][$key]))
			return ;
		$menuItem = $this->creatMenuItem($key,$title,$link,$icon ,$target);
		self::$menu[$fatherKey][] = $menuItem ;
		self::$menuFatherKeys[$key] = $fatherKey ;
		self::$menuKeys[$fatherKey][$key] = count(self::$menu[$fatherKey]) - 1 ;
	}

	public function after($afterKey ,$key , $title,$link = '#',$icon = null,$target = '', $fatherKey = null){
		if ( $fatherKey == null )
			$fatherKey = $this->menuTitle ;
		else
			$fatherKey = $this->generateChildName ($fatherKey) ;

		if ( ! isset(self::$menuKeys[$fatherKey][$afterKey]))
			return ;
		$menuItem = $this->creatMenuItem($key,$title,$link,$icon ,$target);
		array_splice(self::$menu[$fatherKey] ,self::$menuKeys[$fatherKey][$afterKey]+1 , 0 , [$menuItem] );
		foreach ( self::$menuKeys[$fatherKey] as $menuKey => $value )
			if ( $value > self::$menuKeys[$fatherKey][$afterKey] )
				self::$menuKeys[$fatherKey][$menuKey]++;
		self::$menuKeys[$fatherKey][$key] = self::$menuKeys[$fatherKey][$afterKey]+ 1 ;
		self::$menuFatherKeys[$key] = $fatherKey ;
	}

	public function before($beforeKey ,$key , $title,$link = '#',$icon = null,$target = '', $fatherKey = null){
		if ( $fatherKey == null )
			$fatherKey = $this->menuTitle ;
		else
			$fatherKey = $this->generateChildName ($fatherKey) ;

		if ( ! isset(self::$menuKeys[$fatherKey][$beforeKey]))
			return ;
		$menuItem = $this->creatMenuItem($key,$title,$link,$icon ,$target);
		array_splice(self::$menu[$fatherKey] ,self::$menuKeys[$fatherKey][$beforeKey]  , 0 , [$menuItem] );
		self::$menuKeys[$fatherKey][$key] = self::$menuKeys[$fatherKey][$beforeKey] ;
		self::$menuFatherKeys[$key] = $fatherKey ;
		foreach ( self::$menuKeys[$fatherKey] as $menuKey => $value )
			if ( $value >= self::$menuKeys[$fatherKey][$beforeKey] )
				self::$menuKeys[$fatherKey][$menuKey]++;

	}

	public function addChild($fatherKey , $key , $title,$link = '#',$icon = null,$target = ''){
		if ( ! isset(self::$menuFatherKeys[$fatherKey]))
			return ;
		$fatherKeyOfFather = self::$menuFatherKeys[$fatherKey] ;
		$menuItem = $this->creatMenuItem($key,$title,$link,$icon ,$target);
		$childKeyName = $this->generateChildName ($fatherKey) ;
		if ( isset(self::$menuKeys[$childKeyName][$key]))
			return ;
		self::$menu[$childKeyName][] = $menuItem ;
		self::$menu[$fatherKeyOfFather][ self::$menuKeys[$fatherKeyOfFather][$fatherKey] ] ->setChild($childKeyName) ;
		self::$menuKeys[$childKeyName][$key] = count(self::$menu[$childKeyName]) - 1 ;
	}

	private function creatMenuItem($key , $title,$link = '#',$icon = null,$target = ''){
		$menuItem = new menuItem();
		$menuItem->setKey($key);
		$menuItem->setTitle($title);
		$menuItem->setLink($link);
		$menuItem->setIcon($icon);
		$menuItem->setTarget($target);
		return $menuItem ;
	}

	private function generateChildName ($fatherKey) {
		return 'Temp_'.md5($fatherKey) ;
	}
}