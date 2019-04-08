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

class api extends model implements modelInterFace  {

	private $primaryKey = 'apiId';
	private $primaryKeyShouldNotInsertOrUpdate = 'apiId';
	private $apiId ;
	private $name ;
	private $active ;
	private $domain ;
	private $allowIp ;


	public function setFromArray($result) {
		$this->apiId = $result['apiId'] ;
		$this->name = $result['name'] ;
		$this->active = $result['active'] ;
		$this->domain = $result['domain'] ;
		$this->allowIp = $result['allowIp'] ;
	}

	public function setApiId( $apiId = null ) {
		$this->apiId = $apiId ;
	}


	public function setName( $name = null ) {
		$this->name = $name ;
	}


	public function setActive( $active = null ) {
		$this->active = $active ;
	}


	public function setDomain( $domain = null ) {
		$this->domain = $domain ;
	}


	public function setAllowIp( $allowIp = null ) {
		$this->allowIp = $allowIp ;
	}


	public function getApiId() {
		return $this->apiId ;
	}


	public function getName() {
		return $this->name ;
	}


	public function getActive() {
		return $this->active ;
	}


	public function getDomain() {
		return $this->domain ;
	}


	public function getAllowIp() {
		return $this->allowIp ;
	}



	public function returnAsArray( ) {
		$array['apiId'] = $this->apiId ;
		$array['name'] = $this->name ;
		$array['active'] = $this->active ;
		$array['domain'] = $this->domain ;
		$array['allowIp'] = $this->allowIp ;
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


}
