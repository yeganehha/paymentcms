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

class transactions extends model implements modelInterFace {

	private $primaryKey = 'transactionId';
	private $primaryKeyShouldNotInsertOrUpdate = 'transactionId';
	private $transactionId ;
	private $invoiceId ;
	private $price ;
	private $time ;
	private $ip ;
	private $module ;
	private $status ;
	private $transactionCodeOne ;
	private $transactionCodeTwo ;
	private $description ;

	public function setFromArray($result) {
		$this->transactionId = $result['transactionId'];
		$this->invoiceId = $result['invoiceId'];
		$this->price = $result['price'];
		$this->time = $result['time'];
		$this->ip = $result['ip'];
		$this->module = $result['module'];
		$this->status = $result['status'];
		$this->transactionCodeOne = $result['transactionCodeOne'];
		$this->transactionCodeTwo = $result['transactionCodeTwo'];
		$this->description = $result['description'];
	}


	public function setTransactionId( $transactionId = null ) {
		$this->transactionId = $transactionId ;
	}


	public function setInvoiceId( $invoiceId = null ) {
		$this->invoiceId = $invoiceId ;
	}


	public function setPrice( $price = null ) {
		$this->price = $price ;
	}


	public function setTime( $time = null ) {
		$this->time = $time ;
	}


	public function setIp( $ip = null ) {
		$this->ip = $ip ;
	}


	public function setModule( $module = null ) {
		$this->module = $module ;
	}


	public function setStatus( $status = null ) {
		$this->status = $status ;
	}


	public function setTransactionCodeOne( $transactionCodeOne = null ) {
		$this->transactionCodeOne = $transactionCodeOne ;
	}


	public function setTransactionCodeTwo( $transactionCodeTwo = null ) {
		$this->transactionCodeTwo = $transactionCodeTwo ;
	}


	public function setDescription( $description = null ) {
		$this->description = $description ;
	}


	public function getTransactionId() {
		return $this->transactionId ;
	}


	public function getInvoiceId() {
		return $this->invoiceId ;
	}


	public function getPrice() {
		return $this->price ;
	}


	public function getTime() {
		return $this->time ;
	}


	public function getIp() {
		return $this->ip ;
	}


	public function getModule() {
		return $this->module ;
	}


	public function getStatus() {
		return $this->status ;
	}


	public function getTransactionCodeOne() {
		return $this->transactionCodeOne ;
	}


	public function getTransactionCodeTwo() {
		return $this->transactionCodeTwo ;
	}


	public function getDescription() {
		return $this->description ;
	}


	public function returnAsArray( ) {
		$array['transactionId'] = $this->transactionId ;
		$array['invoiceId'] = $this->invoiceId ;
		$array['price'] = $this->price ;
		$array['time'] = $this->time ;
		$array['ip'] = $this->ip ;
		$array['module'] = $this->module ;
		$array['status'] = $this->status ;
		$array['transactionCodeOne'] = $this->transactionCodeOne ;
		$array['transactionCodeTwo'] = $this->transactionCodeTwo ;
		$array['description'] = $this->description ;
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
