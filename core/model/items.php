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

class items extends model implements modelInterFace {

	private $primaryKey = 'itemId';
	private $primaryKeyShouldNotInsertOrUpdate = 'itemId';
	private $itemId ;
	private $factorId ;
	private $price ;
	private $description ;
	private $time ;
	private $serviceId ;

	public function setFromArray($result) {
		$this->itemId = $result['itemId'] ;
		$this->factorId = $result['factorId'] ;
		$this->price = $result['price'] ;
		$this->description = $result['description'] ;
		$this->time = $result['time'] ;
		$this->serviceId = $result['serviceId'] ;
	}



	public function setItemId( $itemId = null ) {
		$this->itemId = $itemId ;
	}


	public function setFactorId( $factorId = null ) {
		$this->factorId = $factorId ;
	}


	public function setPrice( $price = null ) {
		$this->price = $price ;
	}


	public function setDescription( $description = null ) {
		$this->description = $description ;
	}


	public function setTime( $time = null ) {
		$this->time = $time ;
	}


	public function setServiceId( $serviceId = null ) {
		$this->serviceId = $serviceId ;
	}


	public function getItemId() {
		return $this->itemId ;
	}


	public function getFactorId() {
		return $this->factorId ;
	}


	public function getPrice() {
		return $this->price ;
	}


	public function getDescription() {
		return $this->description ;
	}


	public function getTime() {
		return $this->time ;
	}


	public function getServiceId() {
		return $this->serviceId ;
	}

	public function returnAsArray( ) {
		$array['itemId'] = $this->itemId ;
		$array['factorId'] = $this->factorId ;
		$array['price'] = $this->price ;
		$array['description'] = $this->description ;
		$array['time'] = $this->time ;
		$array['serviceId'] = $this->serviceId ;
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
