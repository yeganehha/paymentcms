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

class site_statistics extends model implements modelInterFace {

	private $primaryKey = 'siteUrl';
	private $primaryKeyShouldNotInsertOrUpdate = 'id';
	private $id ;
	private $siteUrl ;
	private $versionNumber ;
	private $register_time ;
	private $defultLang ;
	private $listOfVersion;
	private $listOfDefultLang;
	private $lastTimeModify;

	public function setFromArray($result) {
		$this->id = $result['id'] ;
		$this->siteUrl = $result['siteUrl'] ;
		$this->versionNumber = $result['versionNumber'] ;
		$this->register_time = $result['register_time'] ;
		$this->defultLang = $result['defultLang'] ;
		$this->listOfVersion = $result['listOfVersion'] ;
		$this->listOfDefultLang = $result['listOfDefultLang'] ;
		$this->lastTimeModify = $result['lastTimeModify'] ;
	}


	public function returnAsArray( ) {
		$array['id'] = $this->id ;
		$array['siteUrl'] = $this->siteUrl ;
		$array['versionNumber'] = $this->versionNumber ;
		$array['register_time'] = $this->register_time ;
		$array['defultLang'] = $this->defultLang ;
		$array['listOfVersion'] = $this->listOfVersion ;
		$array['lastTimeModify'] = $this->lastTimeModify ;
		$array['listOfDefultLang'] = $this->listOfDefultLang ;
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
	public function getId() {
		return $this->id;
	}

	/**
	 * @param mixed $id
	 */
	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * @return mixed
	 */
	public function getSiteUrl() {
		return $this->siteUrl;
	}

	/**
	 * @param mixed $siteUrl
	 */
	public function setSiteUrl($siteUrl) {
		$this->siteUrl = $siteUrl;
	}

	/**
	 * @return mixed
	 */
	public function getVersionNumber() {
		return $this->versionNumber;
	}

	/**
	 * @param mixed $versionNumber
	 */
	public function setVersionNumber($versionNumber) {
		$this->versionNumber = $versionNumber;
	}

	/**
	 * @return mixed
	 */
	public function getRegisterTime() {
		return $this->register_time;
	}

	/**
	 * @param mixed $register_time
	 */
	public function setRegisterTime($register_time) {
		$this->register_time = $register_time;
	}

	/**
	 * @return mixed
	 */
	public function getDefultLang() {
		return $this->defultLang;
	}

	/**
	 * @param mixed $defultLang
	 */
	public function setDefultLang($defultLang) {
		$this->defultLang = $defultLang;
	}

	/**
	 * @return mixed
	 */
	public function getListOfVersion() {
		return $this->listOfVersion;
	}

	/**
	 * @param mixed $listOfVersion
	 */
	public function setListOfVersion($listOfVersion) {
		$this->listOfVersion = $listOfVersion;
	}

	/**
	 * @return mixed
	 */
	public function getListOfDefultLang() {
		return $this->listOfDefultLang;
	}

	/**
	 * @param mixed $listOfDefultLang
	 */
	public function setListOfDefultLang($listOfDefultLang) {
		$this->listOfDefultLang = $listOfDefultLang;
	}

	/**
	 * @return mixed
	 */
	public function getLastTimeModify() {
		return $this->lastTimeModify;
	}

	/**
	 * @param mixed $lastTimeModify
	 */
	public function setLastTimeModify($lastTimeModify) {
		$this->lastTimeModify = $lastTimeModify;
	}

}
