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

use paymentCms\component\model;
use paymentCms\component\security;
use paymentCms\model\modelInterFace ;

class user extends model implements modelInterFace {

	private $primaryKey = 'userId';
	private $primaryKeyShouldNotInsertOrUpdate = 'userId';
	private $userId ;
	private $fname ;
	private $lname ;
	private $email ;
	private $phone ;
	private $register_time;
	private $block;
	private $admin_note;
	private $password;
	private $user_group_id;

	public function setFromArray($result) {
		$this->userId = $result['userId'] ;
		$this->fname = $result['fname'] ;
		$this->lname = $result['lname'] ;
		$this->email = $result['email'] ;
		$this->phone = $result['phone'] ;
		$this->register_time = $result['register_time'] ;
		$this->block = $result['block'] ;
		$this->admin_note = $result['admin_note'] ;
		$this->password = $result['password'] ;
		$this->user_group_id = $result['user_group_id'] ;
	}



	public function setUserId( $userId = null ) {
		$this->userId = $userId ;
	}


	public function setFname( $fname = null ) {
		$this->fname = $fname ;
	}


	public function setLname( $lname = null ) {
		$this->lname = $lname ;
	}


	public function getName($explode = " "){
		return $this->fname . $explode . $this->lname ;
	}

	public function setEmail( $email = null ) {
		$this->email = $email ;
	}


	public function setPhone( $phone = null ) {
		$this->phone = $phone ;
	}


	public function getUserId() {
		return $this->userId ;
	}


	public function getFname() {
		return $this->fname ;
	}


	public function getLname() {
		return $this->lname ;
	}


	public function getEmail() {
		return $this->email ;
	}


	public function getPhone() {
		return $this->phone ;
	}



	public function returnAsArray( ) {
		$array['userId'] = $this->userId ;
		$array['fname'] = $this->fname ;
		$array['lname'] = $this->lname ;
		$array['email'] = $this->email ;
		$array['phone'] = $this->phone ;
		$array['register_time'] = $this->register_time ;
		$array['admin_note'] = $this->admin_note ;
		$array['block'] = $this->block ;
		$array['password'] = $this->password ;
		$array['user_group_id'] = $this->user_group_id ;
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
	public function getBlock() {
		return $this->block;
	}

	/**
	 * @param mixed $block
	 */
	public function setBlock($block) {
		$this->block = $block;
	}

	/**
	 * @return mixed
	 */
	public function getAdminNote() {
		return $this->admin_note;
	}

	/**
	 * @param mixed $admin_note
	 */
	public function setAdminNote($admin_note) {
		$this->admin_note = $admin_note;
	}

	/**
	 * @param mixed $password
	 */
	public function setPassword($password) {
		$this->password = security::encrypt($password);
	}

	/**
	 * @return mixed
	 */
	public function getUserGroupId() {
		return $this->user_group_id;
	}

	/**
	 * @param mixed $user_group_id
	 */
	public function setUserGroupId($user_group_id) {
		$this->user_group_id = $user_group_id;
	}

	/**
	 * @return mixed
	 */
	public function getPassword() {
		return $this->password;
	}


}
