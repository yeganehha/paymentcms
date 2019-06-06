<?php 



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


namespace App\paymentServer\model;

use paymentCms\component\model;
use paymentCms\model\modelInterFace ;

class bugs extends model implements modelInterFace {

	private $primaryKey = 'bugId';
	private $primaryKeyShouldNotInsertOrUpdate = 'bugId';
	private $bugId;
	private $file;
	private $line;
	private $message;
	private $type;
	private $version;
	private $fixed_version;
	private $url;
	private $app;
	private $app_provider;
	private $controller;
	private $method;
	private $server;
	private $post;
	private $get_data;
	private $session;
	private $cookie;
	private $crash_report_time;
	private $php;

	public function setFromArray($result) {
		$this->bugId = $result['bugId'];
		$this->file = $result['file'];
		$this->line = $result['line'];
		$this->message = $result['message'];
		$this->type = $result['type'];
		$this->version = $result['version'];
		$this->fixed_version = $result['fixed_version'];
		$this->url = $result['url'];
		$this->app = $result['app'];
		$this->app_provider = $result['app_provider'];
		$this->controller = $result['controller'];
		$this->method = $result['method'];
		$this->server = $result['server'];
		$this->post = $result['post'];
		$this->get_data = $result['get_data'];
		$this->session = $result['session'];
		$this->cookie = $result['cookie'];
		$this->crash_report_time = $result['crash_report_time'];
		$this->php = $result['php'];
	}


	public function returnAsArray() {
		$array['bugId'] = $this->bugId;
		$array['file'] = $this->file;
		$array['line'] = $this->line;
		$array['message'] = $this->message;
		$array['type'] = $this->type;
		$array['version'] = $this->version;
		$array['fixed_version'] = $this->fixed_version;
		$array['url'] = $this->url;
		$array['app'] = $this->app;
		$array['app_provider'] = $this->app_provider;
		$array['controller'] = $this->controller;
		$array['method'] = $this->method;
		$array['server'] = $this->server;
		$array['post'] = $this->post;
		$array['get_data'] = $this->get_data;
		$array['session'] = $this->session;
		$array['cookie'] = $this->cookie;
		$array['crash_report_time'] = $this->crash_report_time;
		$array['php'] = $this->php;
		return $array;
	}

	/**
	 * @return string
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
	public function getBugId() {
		return $this->bugId;
	}

	/**
	 * @param mixed $bugId
	 */
	public function setBugId($bugId) {
		$this->bugId = $bugId;
	}

	/**
	 * @return mixed
	 */
	public function getFile() {
		return $this->file;
	}

	/**
	 * @param mixed $file
	 */
	public function setFile($file) {
		$this->file = $file;
	}

	/**
	 * @return mixed
	 */
	public function getLine() {
		return $this->line;
	}

	/**
	 * @param mixed $line
	 */
	public function setLine($line) {
		$this->line = $line;
	}

	/**
	 * @return mixed
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 * @param mixed $message
	 */
	public function setMessage($message) {
		$this->message = $message;
	}

	/**
	 * @return mixed
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @param mixed $type
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
	 * @return mixed
	 */
	public function getVersion() {
		return $this->version;
	}

	/**
	 * @param mixed $version
	 */
	public function setVersion($version) {
		$this->version = $version;
	}

	/**
	 * @return mixed
	 */
	public function getFixedVersion() {
		return $this->fixed_version;
	}

	/**
	 * @param mixed $fixed_version
	 */
	public function setFixedVersion($fixed_version) {
		$this->fixed_version = $fixed_version;
	}

	/**
	 * @return mixed
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * @param mixed $url
	 */
	public function setUrl($url) {
		$this->url = $url;
	}

	/**
	 * @return mixed
	 */
	public function getPost() {
		return $this->post;
	}

	/**
	 * @param mixed $post
	 */
	public function setPost($post) {
		$this->post = $post;
	}

	/**
	 * @return mixed
	 */
	public function getServer() {
		return $this->server;
	}

	/**
	 * @param mixed $server
	 */
	public function setServer($server) {
		$this->server = $server;
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
	public function getGetData() {
		return $this->get_data;
	}

	/**
	 * @param mixed $get_data
	 */
	public function setGetData($get_data) {
		$this->get_data = $get_data;
	}

	/**
	 * @return mixed
	 */
	public function getSession() {
		return $this->session;
	}

	/**
	 * @param mixed $session
	 */
	public function setSession($session) {
		$this->session = $session;
	}

	/**
	 * @return mixed
	 */
	public function getCookie() {
		return $this->cookie;
	}

	/**
	 * @param mixed $cookie
	 */
	public function setCookie($cookie) {
		$this->cookie = $cookie;
	}

	/**
	 * @return mixed
	 */
	public function getCrashReportTime() {
		return $this->crash_report_time;
	}

	/**
	 * @param mixed $crash_report_time
	 */
	public function setCrashReportTime($crash_report_time) {
		$this->crash_report_time = $crash_report_time;
	}

	/**
	 * @return mixed
	 */
	public function getPhp() {
		return $this->php;
	}

	/**
	 * @param mixed $php
	 */
	public function setPhp($php) {
		$this->php = $php;
	}


}