<?php


namespace App\log\model;
// *************************************************************************
// *                                                                       *
// * TableClass - The Complete Table To Class PHP Function                 *
// * Copyright (c) Erfan Ebrahimi. All Rights Reserved,                    *
// * BuildId: 1                                                            *
// *                                                                       *
// *************************************************************************
// *                                                                       *
// * Email: persionhost@gmail.com                                          *
// * phone: 09361090413                                                    *
// *                                                                       *
// *                                                                       *
// *************************************************************************


use paymentCms\component\browser;
use paymentCms\component\model;
use paymentCms\component\security;
use paymentCms\model\modelInterFace ;

class log extends model implements modelInterFace {

	private $primaryKey = ['logId','userId'];
	private $primaryKeyShouldNotInsertOrUpdate = 'logId';
	private $logId ;
	private $userId ;
	private $app ;
	private $app_provider ;
	private $controller ;
	private $method ;
	private $log_name ;
	private $description ;
	private $previous_page ;
	private $current_url ;
	private $ip ;
	private $browser ;
	private $platform ;
	private $activity_time ;

	public function setFromArray($result) {
		$this->logId = $result['logId'] ;
		$this->userId = $result['userId'] ;
		$this->app = $result['app'] ;
		$this->app_provider = $result['app_provider'] ;
		$this->controller = $result['controller'] ;
		$this->method = $result['method'] ;
		$this->log_name = $result['log_name'] ;
		$this->description = $result['description'] ;
		$this->previous_page = $result['previous_page'] ;
		$this->current_url = $result['current_url'] ;
		$this->ip = $result['ip'] ;
		$this->browser = $result['browser'] ;
		$this->platform = $result['platform'] ;
		$this->activity_time = $result['activity_time'] ;
	}

	public function returnAsArray( ) {
		$array['logId'] = $this->logId ;
		$array['userId'] = $this->userId ;
		$array['app'] = $this->app ;
		$array['app_provider'] = $this->app_provider ;
		$array['controller'] = $this->controller ;
		$array['method'] = $this->method ;
		$array['description'] = $this->description ;
		$array['log_name'] = $this->log_name ;
		$array['previous_page'] = $this->previous_page ;
		$array['current_url'] = $this->current_url ;
		$array['ip'] = $this->ip ;
		$array['browser'] = $this->browser ;
		$array['platform'] = $this->platform ;
		$array['activity_time'] = $this->activity_time ;
		return $array ;
	}

	/**
	 * @return array
	 */
	public function getPrimaryKey() {
		return $this->primaryKey;
	}

	/**
	 * @return string
	 */
	public function getPrimaryKeyShouldNotInsertOrUpdate() {
		return $this->primaryKeyShouldNotInsertOrUpdate;
	}

	/**
	 * @return mixed
	 */
	public function getApp() {
		return $this->app;
	}

	/**
	 * @param mixed $app
	 */
	public function setApp($app) {
		$this->app = $app;
	}

	/**
	 * @return mixed
	 */
	public function getLogId() {
		return $this->logId;
	}

	/**
	 * @param mixed $logId
	 */
	public function setLogId($logId) {
		$this->logId = $logId;
	}

	/**
	 * @return mixed
	 */
	public function getUserId() {
		return $this->userId;
	}

	/**
	 * @param mixed $userId
	 */
	public function setUserId($userId) {
		$this->userId = $userId;
	}

	/**
	 * @return mixed
	 */
	public function getAppProvider() {
		return $this->app_provider;
	}

	/**
	 * @param mixed $app_provider
	 */
	public function setAppProvider($app_provider) {
		$this->app_provider = $app_provider;
	}

	/**
	 * @return mixed
	 */
	public function getController() {
		return $this->controller;
	}

	/**
	 * @param mixed $controller
	 */
	public function setController($controller) {
		$this->controller = $controller;
	}

	/**
	 * @return mixed
	 */
	public function getMethod() {
		return $this->method;
	}

	/**
	 * @param mixed $method
	 */
	public function setMethod($method) {
		$this->method = $method;
	}

	/**
	 * @return mixed
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @param mixed $description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * @return mixed
	 */
	public function getPreviousPage() {
		return $this->previous_page;
	}

	/**
	 * @param mixed $previous_page
	 */
	public function setPreviousPage($previous_page) {
		$this->previous_page = $previous_page;
	}

	/**
	 * @return mixed
	 */
	public function getCurrentUrl() {
		return $this->current_url;
	}

	/**
	 * @param mixed $current_url
	 */
	public function setCurrentUrl($current_url) {
		$this->current_url = $current_url;
	}

	/**
	 * @return mixed
	 */
	public function getIp() {
		return $this->ip;
	}

	/**
	 * @param mixed $ip
	 */
	public function setIp($ip = null) {
		if ( $ip == null ) $ip = security::getIp();
		$this->ip = $ip;
	}

	/**
	 * @return mixed
	 */
	public function getBrowser() {
		return $this->browser;
	}

	/**
	 * @param mixed $browser
	 */
	public function setBrowser($browser = null) {
		if ( $browser == null ){
			$browserClass = new browser();
			$this->setPlatform( $browserClass->getPlatform() ) ;
			$browser =  $browserClass->getBrowser();
			$browser .= ' V: '. $browserClass->getVersion();
		}
		$this->browser = $browser;
	}

	/**
	 * @return mixed
	 */
	public function getActivityTime() {
		return $this->activity_time;
	}

	/**
	 * @param mixed $activity_time
	 */
	public function setActivityTime($activity_time = null) {
		if ( $activity_time == null )
			$activity_time = date('Y-m-d H:i:s');
		$this->activity_time = $activity_time;
	}

	/**
	 * @return mixed
	 */
	public function getPlatform() {
		return $this->platform;
	}

	/**
	 * @param mixed $platform
	 */
	public function setPlatform($platform) {
		$this->platform = $platform;
	}

	/**
	 * @return mixed
	 */
	public function getLogName() {
		return $this->log_name;
	}

	/**
	 * @param mixed $log_name
	 */
	public function setLogName($log_name) {
		$this->log_name = $log_name;
	}

	public function getUserLog($userId , $show_view_page = false , $fields = '*' ,$orderBy = null ,$limit = null , $groupBy = null){
		return $this->search([$userId,'view_webSite_page'],' userId = ? and log_name '.( $show_view_page ? '' : '!' ) .'= ?' ,null ,$fields,$orderBy,$limit,$groupBy);
	}

}
