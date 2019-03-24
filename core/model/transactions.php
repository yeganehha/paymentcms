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


use paymentCms\model\model ;

class transactions implements model {


	private $transactionId ;
	private $factorId ;
	private $price ;
	private $time ;
	private $ip ;
	private $module ;
	private $status ;
	private $transactionCodeOne ;
	private $transactionCodeTwo ;
	private $description ;

	public function __construct(  $searchVariable = null , $searchWhereClaus = 'transactionId = ? ' ){
		if ( $searchVariable != null ) {
			$result = \database::searche('transactions' ,  $searchWhereClaus  , array($searchVariable) ); 
			if ( $result != null ) {
				$this->transactionId = $result['transactionId'] ;
				$this->factorId = $result['factorId'] ;
				$this->price = $result['price'] ;
				$this->time = $result['time'] ;
				$this->ip = $result['ip'] ;
				$this->module = $result['module'] ;
				$this->status = $result['status'] ;
				$this->transactionCodeOne = $result['transactionCodeOne'] ;
				$this->transactionCodeTwo = $result['transactionCodeTwo'] ;
				$this->description = $result['description'] ;
			} else 
				return $this->returning(null,false,'transactions4');
		}
		return $this->returning();
	}

	public function search( $searchVariable, $searchWhereClaus , $tableName = 'transactions'  , $fields = '*' ) {
		$results = \database::searche($tableName, $searchWhereClaus, $searchVariable, true ,false,$fields );
		return $this->returning($results) ;
	}


	public function setTransactionId( $transactionId = null ) {
		$this->transactionId = $transactionId ;
	}


	public function setFactorId( $factorId = null ) {
		$this->factorId = $factorId ;
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


	public function getFactorId() {
		return $this->factorId ;
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


	public function insertToDataBase( ) {
		$array['factorId'] = $this->factorId ;
		$array['price'] = $this->price ;
		$array['time'] = $this->time ;
		$array['ip'] = $this->ip ;
		$array['module'] = $this->module ;
		$array['status'] = $this->status ;
		$array['transactionCodeOne'] = $this->transactionCodeOne ;
		$array['transactionCodeTwo'] = $this->transactionCodeTwo ;
		$array['description'] = $this->description ;
		$id = \database::insert('transactions' , $array  ); 
		if ( $id ) {
			$this->transactionId = $id ; 
			return $this->returning($id) ;
		}
		return $this->returning(null,false,'transactions3') ;
	}


	public function upDateDataBase( ) {
		$array['factorId'] = $this->factorId ;
		$array['price'] = $this->price ;
		$array['time'] = $this->time ;
		$array['ip'] = $this->ip ;
		$array['module'] = $this->module ;
		$array['status'] = $this->status ;
		$array['transactionCodeOne'] = $this->transactionCodeOne ;
		$array['transactionCodeTwo'] = $this->transactionCodeTwo ;
		$array['description'] = $this->description ;
		if ( \database::update('transactions' , $array , array('query' => 'transactionId = ?', 'param' => array($this->transactionId)) ) ) 
			return $this->returning() ;
		return $this->returning(null,false,'transactions2') ;
	}


	public function deleteFromDataBase( ) {
		if ( \database::delete('transactions', array('query' => 'transactionId = ?', 'param' => array($this->transactionId)) ) ) 
			return $this->returning() ;
		return  $this->returning(null,false,'transactions1') ;
	}


	public function returnAsArray( ) {
		$array['transactionId'] = $this->transactionId ;
		$array['factorId'] = $this->factorId ;
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



	private function returning($return = null , $status = true , $errorNumber = "transactions0" , $massagesParams = null ){
		if ( $return == null )
				return $status ;
		else
				return $return ;

	}



}
