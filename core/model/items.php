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

class items implements model {


	private $itemId ;
	private $factorId ;
	private $price ;
	private $description ;
	private $time ;
	private $serviceId ;

	public function __construct(  $searchVariable = null , $searchWhereClaus = 'itemId = ? ' ){
		if ( $searchVariable != null ) {
			$result = \database::searche('items' ,  $searchWhereClaus  , array($searchVariable) ); 
			if ( $result != null ) {
				$this->itemId = $result['itemId'] ;
				$this->factorId = $result['factorId'] ;
				$this->price = $result['price'] ;
				$this->description = $result['description'] ;
				$this->time = $result['time'] ;
				$this->serviceId = $result['serviceId'] ;
			} else 
				return $this->returning(null,false,'items4');
		}
		return $this->returning();
	}

	public function search( $searchVariable, $searchWhereClaus , $tableName = 'items'  , $fields = '*' ) {
		$results = \database::searche($tableName, $searchWhereClaus, $searchVariable, true ,false,$fields );
		return $this->returning($results) ;
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


	public function insertToDataBase( ) {
		$array['factorId'] = $this->factorId ;
		$array['price'] = $this->price ;
		$array['description'] = $this->description ;
		$array['time'] = $this->time ;
		$array['serviceId'] = $this->serviceId ;
		$id = \database::insert('items' , $array  ); 
		if ( $id ) {
			$this->itemId = $id ; 
			return $this->returning($id) ;
		}
		return $this->returning(null,false,'items3') ;
	}


	public function upDateDataBase( ) {
		$array['factorId'] = $this->factorId ;
		$array['price'] = $this->price ;
		$array['description'] = $this->description ;
		$array['time'] = $this->time ;
		$array['serviceId'] = $this->serviceId ;
		if ( \database::update('items' , $array , array('query' => 'itemId = ?', 'param' => array($this->itemId)) ) ) 
			return $this->returning() ;
		return $this->returning(null,false,'items2') ;
	}


	public function deleteFromDataBase( ) {
		if ( \database::delete('items', array('query' => 'itemId = ?', 'param' => array($this->itemId)) ) ) 
			return $this->returning() ;
		return  $this->returning(null,false,'items1') ;
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



	private function returning($return = null , $status = true , $errorNumber = "items0" , $massagesParams = null ){
		if ( $return == null )
				return $status ;
		else
				return $return ;

	}



}
