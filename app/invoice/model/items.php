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


namespace App\invoice\model;

use paymentCms\component\model ;
use paymentCms\model\modelInterFace ;

class items extends model implements modelInterFace {

	private $primaryKey = 'itemId';
	private $primaryKeyShouldNotInsertOrUpdate = 'itemId';
	private $itemId ;
	private $invoiceId ;
	private $price ;
	private $description ;
	private $time ;
	private $serviceId ;

	public function setFromArray($result) {
		$this->itemId = $result['itemId'] ;
		$this->invoiceId = $result['invoiceId'] ;
		$this->price = $result['price'] ;
		$this->description = $result['description'] ;
		$this->time = $result['time'] ;
		$this->serviceId = $result['serviceId'] ;
	}



	public function setItemId( $itemId = null ) {
		$this->itemId = $itemId ;
	}


	public function setInvoiceId( $invoiceId = null ) {
		$this->invoiceId = $invoiceId ;
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


	public function getInvoiceId() {
		return $this->invoiceId ;
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
		$array['invoiceId'] = $this->invoiceId ;
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
