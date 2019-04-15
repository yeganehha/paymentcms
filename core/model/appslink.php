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

class appslink extends model implements modelInterFace  {

	private $primaryKey = ['link'];
	private $primaryKeyShouldNotInsertOrUpdate = null;
	private $link ;
	private $app ;


	public function setFromArray($result) {
		$this->link = $result['link'] ;
		$this->app = $result['app'] ;
	}


	public function returnAsArray( ) {
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


}
