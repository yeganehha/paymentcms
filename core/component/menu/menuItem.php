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


class menuItem {

	private $key = null ;
	public $title = null ;
	public $link = null ;
	private $icon = null ;
	private $target = null ; /* _blank|_self|_parent|_top|framename */
	private $child = null ;
	
	/**
	 * @return null
	 */
	public  function getTitle() {
		return $this->title;
	}

	/**
	 * @param null $title

	 */
	public  function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * @return null
	 */
	public  function getLink() {
		return $this->link;
	}

	/**
	 * @param null $link
	 
	 */
	public  function setLink($link) {
		$this->link = $link;
		
	}

	/**
	 * @return null
	 */
	public  function getIcon() {
		return $this->icon;
	}

	/**
	 * @param null $icon
	 
	 */
	public  function setIcon($icon) {
		$this->icon = $icon;
		
	}

	/**
	 * @return null
	 */
	public  function getTarget() {
		return $this->target;
	}

	/**
	 * @param null $target
	 
	 */
	public  function setTarget($target) {
		$this->target = $target;
		
	}

	/**
	 * @return menu|null
	 */
	public  function getChild() {
		return $this->child;
	}

	/**
	 * @param menu|null $child
	 
	 */
	public  function setChild($child) {
		$this->child = $child;
		
	}

	/**
	 * @return null
	 */
	public  function getKey() {
		return $this->key;
	}

	/**
	 * @param null $key
	 
	 */
	public  function setKey($key) {
		$this->key = $key;
		
	}

}