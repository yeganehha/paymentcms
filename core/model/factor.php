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

class factor implements model {


	private $factorId ;
	private $userId ;
	private $createdDate ;
	private $dueDate ;
	private $paidDate ;
	private $status ;
	private $module ;
	private $price ;
	private $requestAction ;
	private $backUri ;
	private $createdIp ;
	private $apiId ;

	public function __construct(  $searchVariable = null , $searchWhereClaus = 'factorId = ? ' ){
		if ( $searchVariable != null ) {
			$result = \database::searche('factor' ,  $searchWhereClaus  , array($searchVariable) ); 
			if ( $result != null ) {
				$this->factorId = $result['factorId'] ;
				$this->userId = $result['userId'] ;
				$this->createdDate = $result['createdDate'] ;
				$this->dueDate = $result['dueDate'] ;
				$this->paidDate = $result['paidDate'] ;
				$this->status = $result['status'] ;
				$this->module = $result['module'] ;
				$this->price = $result['price'] ;
				$this->requestAction = $result['requestAction'] ;
				$this->backUri = $result['backUri'] ;
				$this->createdIp = $result['createdIp'] ;
				$this->apiId = $result['apiId'] ;
			} else 
				return $this->returning(null,false,'factor4');
		}
		return $this->returning();
	}

	public function search( $searchVariable, $searchWhereClaus , $tableName = 'factor'  , $fields = '*' ) {
		$results = \database::searche($tableName, $searchWhereClaus, $searchVariable, true ,false,$fields );
		return $this->returning($results) ;
	}


	public function setFactorId( $factorId = null ) {
		$this->factorId = $factorId ;
	}


	public function setUserId( $userId = null ) {
		$this->userId = $userId ;
	}


	public function setCreatedDate( $createdDate = null ) {
		$this->createdDate = $createdDate ;
	}


	public function setDueDate( $dueDate = null ) {
		$this->dueDate = $dueDate ;
	}


	public function setPaidDate( $paidDate = null ) {
		$this->paidDate = $paidDate ;
	}


	public function setStatus( $status = null ) {
		$this->status = $status ;
	}


	public function setModule( $module = null ) {
		$this->module = $module ;
	}


	public function setPrice( $price = null ) {
		$this->price = $price ;
	}


	public function setRequestAction( $requestAction = null ) {
		$this->requestAction = $requestAction ;
	}


	public function setBackUri( $backUri = null ) {
		$this->backUri = $backUri ;
	}


	public function setCreatedIp( $createdIp = null ) {
		$this->createdIp = $createdIp ;
	}


	public function setApiId( $apiId = null ) {
		$this->apiId = $apiId ;
	}


	public function getFactorId() {
		return $this->factorId ;
	}


	public function getUserId() {
		return $this->userId ;
	}


	public function getCreatedDate() {
		return $this->createdDate ;
	}


	public function getDueDate() {
		return $this->dueDate ;
	}


	public function getPaidDate() {
		return $this->paidDate ;
	}


	public function getStatus() {
		return $this->status ;
	}


	public function getModule() {
		return $this->module ;
	}


	public function getPrice() {
		return $this->price ;
	}


	public function getRequestAction() {
		return $this->requestAction ;
	}


	public function getBackUri() {
		return $this->backUri ;
	}


	public function getCreatedIp() {
		return $this->createdIp ;
	}


	public function getApiId() {
		return $this->apiId ;
	}


	public function insertToDataBase( ) {
		$array['userId'] = $this->userId ;
		$array['createdDate'] = $this->createdDate ;
		$array['dueDate'] = $this->dueDate ;
		$array['paidDate'] = $this->paidDate ;
		$array['status'] = $this->status ;
		$array['module'] = $this->module ;
		$array['price'] = $this->price ;
		$array['requestAction'] = $this->requestAction ;
		$array['backUri'] = $this->backUri ;
		$array['createdIp'] = $this->createdIp ;
		$array['apiId'] = $this->apiId ;
		$id = \database::insert('factor' , $array  ); 
		if ( $id ) {
			$this->factorId = $id ; 
			return $this->returning($id) ;
		}
		return $this->returning(null,false,'factor3') ;
	}


	public function upDateDataBase( ) {
		$array['userId'] = $this->userId ;
		$array['createdDate'] = $this->createdDate ;
		$array['dueDate'] = $this->dueDate ;
		$array['paidDate'] = $this->paidDate ;
		$array['status'] = $this->status ;
		$array['module'] = $this->module ;
		$array['price'] = $this->price ;
		$array['requestAction'] = $this->requestAction ;
		$array['backUri'] = $this->backUri ;
		$array['createdIp'] = $this->createdIp ;
		$array['apiId'] = $this->apiId ;
		if ( \database::update('factor' , $array , array('query' => 'factorId = ?', 'param' => array($this->factorId)) ) ) 
			return $this->returning() ;
		return $this->returning(null,false,'factor2') ;
	}


	public function deleteFromDataBase( ) {
		if ( \database::delete('factor', array('query' => 'factorId = ?', 'param' => array($this->factorId)) ) ) 
			return $this->returning() ;
		return  $this->returning(null,false,'factor1') ;
	}


	public function returnAsArray( ) {
		$array['factorId'] = $this->factorId ;
		$array['userId'] = $this->userId ;
		$array['createdDate'] = $this->createdDate ;
		$array['dueDate'] = $this->dueDate ;
		$array['paidDate'] = $this->paidDate ;
		$array['status'] = $this->status ;
		$array['module'] = $this->module ;
		$array['price'] = $this->price ;
		$array['requestAction'] = $this->requestAction ;
		$array['backUri'] = $this->backUri ;
		$array['createdIp'] = $this->createdIp ;
		$array['apiId'] = $this->apiId ;
		return $array ;
	}



	private function returning($return = null , $status = true , $errorNumber = "factor0" , $massagesParams = null ){
		if ( $return == null )
				return $status ;
		else
				return $return ;

	}



}
