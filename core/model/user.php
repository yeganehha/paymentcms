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
use paymentCms\model\modelInterFace ;

class user extends model implements modelInterFace {

	private $primaryKey = 'userId';
	private $primaryKeyShouldNotInsertOrUpdate = 'userId';
	private $userId ;
	private $fname ;
	private $lname ;
	private $email ;
	private $phone ;

	public function setFromArray($result) {
		$this->userId = $result['userId'] ;
		$this->fname = $result['fname'] ;
		$this->lname = $result['lname'] ;
		$this->email = $result['email'] ;
		$this->phone = $result['phone'] ;
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
