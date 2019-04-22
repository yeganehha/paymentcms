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

class user_group_permission extends model implements modelInterFace {

	private $primaryKey = 'user_groupId';
	private $primaryKeyShouldNotInsertOrUpdate = null;
	private $user_groupId ;
	private $accessPage ;

	public function setFromArray($result) {
		$this->user_groupId = $result['user_groupId'] ;
		$this->accessPage = $result['accessPage'] ;
	}

	public function returnAsArray( ) {
		$array['user_groupId'] = $this->user_groupId ;
		$array['accessPage'] = $this->accessPage ;
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
	public function getAccessPage() {
		return $this->accessPage;
	}

	/**
	 * @param mixed $accessPage
	 */
	public function setAccessPage($accessPage) {
		$this->accessPage = $accessPage;
	}

}
