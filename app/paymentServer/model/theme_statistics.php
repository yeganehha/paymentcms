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

class theme_statistics extends model implements modelInterFace {

	private $primaryKey = 'siteId';
	private $primaryKeyShouldNotInsertOrUpdate = null;
	private $siteId ;
	private $themeName ;
	private $themeVersion ;
	private $author ;
	private $installed ;
	private $useing;
	private $lastModify;

	public function setFromArray($result) {
		$this->siteId = $result['siteId'] ;
		$this->themeName = $result['themeName'] ;
		$this->themeVersion = $result['themeVersion'] ;
		$this->author = $result['author'] ;
		$this->installed = $result['installed'] ;
		$this->useing = $result['useing'] ;
		$this->lastModify = $result['lastModify'] ;
	}


	public function returnAsArray( ) {
		$array['siteId'] = $this->siteId ;
		$array['themeName'] = $this->themeName ;
		$array['themeVersion'] = $this->themeVersion ;
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
	public function getThemeName() {
		return $this->themeName;
	}

	/**
	 * @param mixed $themeName
	 */
	public function setThemeName($themeName) {
		$this->themeName = $themeName;
	}

	/**
	 * @return mixed
	 */
	public function getThemeVersion() {
		return $this->themeVersion;
	}

	/**
	 * @param mixed $themeVersion
	 */
	public function setThemeVersion($themeVersion) {
		$this->themeVersion = $themeVersion;
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


}
