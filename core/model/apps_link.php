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


namespace paymentCms\model;


use paymentCms\component\model ;

class apps_link extends model implements modelInterFace  {

	private $primaryKey = ['apps_linkId'];
	private $primaryKeyShouldNotInsertOrUpdate = 'apps_linkId';
	private $apps_linkId ;
	private $link ;
	private $app ;


	public function setFromArray($result) {
		$this->apps_linkId = $result['apps_linkId'] ;
		$this->link = $result['link'] ;
		$this->app = $result['app'] ;
	}


	public function returnAsArray( ) {
		$array['apps_linkId'] = $this->apps_linkId ;
		$array['link'] = $this->link ;
		$array['app'] = $this->app ;
		return $array ;
	}

	/**
	 * @return array
	 */
	public function getPrimaryKey() {
		return $this->primaryKey;
	}

	/**
	 * @return null
	 */
	public function getPrimaryKeyShouldNotInsertOrUpdate() {
		return $this->primaryKeyShouldNotInsertOrUpdate;
	}

	/**
	 * @return mixed
	 */
	public function getLink() {
		return $this->link;
	}

	/**
	 * @param mixed $link
	 */
	public function setLink($link) {
		$this->link = $link;
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
	public function getAppsLinkId() {
		return $this->apps_linkId;
	}

	/**
	 * @param mixed $apps_linkId
	 */
	public function setAppsLinkId($apps_linkId) {
		$this->apps_linkId = $apps_linkId;
	}


}
