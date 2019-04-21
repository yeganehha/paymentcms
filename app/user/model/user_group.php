<?php


namespace App\user\model;
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




use paymentCms\component\model;
use paymentCms\model\modelInterFace ;

class user_group extends model implements modelInterFace {

	private $primaryKey = 'user_groupId';
	private $primaryKeyShouldNotInsertOrUpdate = 'user_groupId';
	private $user_groupId ;
	private $name ;
	private $loginRequired ;

	public function setFromArray($result) {
		$this->user_groupId = $result['user_groupId'] ;
		$this->name = $result['name'] ;
		$this->loginRequired = $result['loginRequired'] ;
	}

	public function returnAsArray( ) {
		$array['user_groupId'] = $this->user_groupId ;
		$array['name'] = $this->name ;
		$array['loginRequired'] = $this->loginRequired ;
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
	public function getUserGroupId() {
		return $this->user_groupId;
	}

	/**
	 * @param mixed $user_groupId
	 */
	public function setUserGroupId($user_groupId) {
		$this->user_groupId = $user_groupId;
	}

	/**
	 * @return mixed
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param mixed $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @return mixed
	 */
	public function getLoginRequired() {
		return $this->loginRequired;
	}

	/**
	 * @param mixed $loginRequired
	 */
	public function setLoginRequired($loginRequired) {
		$this->loginRequired = $loginRequired;
	}


}
