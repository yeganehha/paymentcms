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

class plugin_statistics extends model implements modelInterFace {

	private $primaryKey = ['siteId','pluginName'];
	private $primaryKeyShouldNotInsertOrUpdate = null;
	private $siteId ;
	private $pluginName ;
	private $pluginVersion ;
	private $author ;
	private $installed ;
	private $useing;
	private $lastModify;

	public function setFromArray($result) {
		$this->siteId = $result['siteId'] ;
		$this->pluginName = $result['pluginName'] ;
		$this->pluginVersion = $result['pluginVersion'] ;
		$this->author = $result['author'] ;
		$this->installed = $result['installed'] ;
		$this->useing = $result['useing'] ;
		$this->lastModify = $result['lastModify'] ;
	}


	public function returnAsArray( ) {
		$array['siteId'] = $this->siteId ;
		$array['pluginName'] = $this->pluginName ;
		$array['pluginVersion'] = $this->pluginVersion ;
		$array['author'] = $this->author ;
		$array['installed'] = $this->installed ;
		$array['useing'] = $this->useing ;
		$array['lastModify'] = $this->lastModify ;
		return $array ;
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
	public function getSiteId() {
		return $this->siteId;
	}

	/**
	 * @param mixed $siteId
	 */
	public function setSiteId($siteId) {
		$this->siteId = $siteId;
	}

	/**
	 * @return mixed
	 */
	public function getAuthor() {
		return $this->author;
	}

	/**
	 * @param mixed $author
	 */
	public function setAuthor($author) {
		$this->author = $author;
	}

	/**
	 * @return mixed
	 */
	public function getInstalled() {
		return $this->installed;
	}

	/**
	 * @param mixed $installed
	 */
	public function setInstalled($installed) {
		$this->installed = $installed;
	}

	/**
	 * @return mixed
	 */
	public function getUseing() {
		return $this->useing;
	}

	/**
	 * @param mixed $useing
	 */
	public function setUseing($useing) {
		$this->useing = $useing;
	}

	/**
	 * @return mixed
	 */
	public function getLastModify() {
		return $this->lastModify;
	}

	/**
	 * @param mixed $lastModify
	 */
	public function setLastModify($lastModify) {
		$this->lastModify = $lastModify;
	}

	/**
	 * @return mixed
	 */
	public function getPluginName() {
		return $this->pluginName;
	}

	/**
	 * @param mixed $pluginName
	 */
	public function setPluginName($pluginName) {
		$this->pluginName = $pluginName;
	}

	/**
	 * @return mixed
	 */
	public function getPluginVersion() {
		return $this->pluginVersion;
	}

	/**
	 * @param mixed $pluginVersion
	 */
	public function setPluginVersion($pluginVersion) {
		$this->pluginVersion = $pluginVersion;
	}


}
