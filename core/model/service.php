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

class service extends model implements modelInterFace  {

	private $primaryKey = 'serviceId';
	private $primaryKeyShouldNotInsertOrUpdate = 'serviceId';
	private $serviceId ;
	private $link ;
	private $price ;
	private $description ;
	private $name ;
	private $status ;
	private $lastNameStatus ;
	private $firstNameStatus ;
	private $emailStatus ;
	private $phoneStatus ;


	public function setFromArray($array){
		$this->serviceId = $array['serviceId'] ;
		$this->link = $array['link'] ;
		$this->price = $array['price'] ;
		$this->description = $array['description'] ;
		$this->name = $array['name'] ;
		$this->status = $array['status'] ;
		$this->lastNameStatus = $array['lastNameStatus'] ;
		$this->firstNameStatus = $array['firstNameStatus'] ;
		$this->emailStatus = $array['emailStatus'] ;
		$this->phoneStatus = $array['phoneStatus'] ;
	}


	public function setServiceId( $serviceId = null ) {
		$this->serviceId = $serviceId ;
	}


	public function setLink( $link = null ) {
		$this->link = $link ;
	}


	public function setPrice( $price = null ) {
		$this->price = $price ;
	}


	public function setDescription( $description = null ) {
		$this->description = $description ;
	}


	public function setName( $name = null ) {
		$this->name = $name ;
	}


	public function setStatus( $status = null ) {
		$this->status = $status ;
	}


	public function setLastNameStatus( $lastNameStatus = null ) {
		$this->lastNameStatus = $lastNameStatus ;
	}


	public function setFirstNameStatus( $firstNameStatus = null ) {
		$this->firstNameStatus = $firstNameStatus ;
	}


	public function setEmailStatus( $emailStatus = null ) {
		$this->emailStatus = $emailStatus ;
	}


	public function setPhoneStatus( $phoneStatus = null ) {
		$this->phoneStatus = $phoneStatus ;
	}


	public function getServiceId() {
		return $this->serviceId ;
	}


	public function getLink() {
		return $this->link ;
	}


	public function getPrice() {
		return $this->price ;
	}


	public function getDescription() {
		return $this->description ;
	}


	public function getName() {
		return $this->name ;
	}


	public function getStatus() {
		return $this->status ;
	}


	public function getLastNameStatus() {
		return $this->lastNameStatus ;
	}


	public function getFirstNameStatus() {
		return $this->firstNameStatus ;
	}


	public function getEmailStatus() {
		return $this->emailStatus ;
	}


	public function getPhoneStatus() {
		return $this->phoneStatus ;
	}



	public function returnAsArray( ) {
		$array['serviceId'] = $this->serviceId ;
		$array['link'] = $this->link ;
		$array['price'] = $this->price ;
		$array['description'] = $this->description ;
		$array['name'] = $this->name ;
		$array['status'] = $this->status ;
		$array['lastNameStatus'] = $this->lastNameStatus ;
		$array['firstNameStatus'] = $this->firstNameStatus ;
		$array['emailStatus'] = $this->emailStatus ;
		$array['phoneStatus'] = $this->phoneStatus ;
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
