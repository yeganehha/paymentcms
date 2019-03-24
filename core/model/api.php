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

class api implements model {


	private $apiId ;
	private $name ;
	private $active ;
	private $hosted ;
	private $allowIp ;

	public function __construct(  $searchVariable = null , $searchWhereClaus = 'apiId = ? ' ){
		if ( $searchVariable != null ) {
			$result = \database::searche('api' ,  $searchWhereClaus  , array($searchVariable) ); 
			if ( $result != null ) {
				$this->apiId = $result['apiId'] ;
				$this->name = $result['name'] ;
				$this->active = $result['active'] ;
				$this->hosted = $result['hosted'] ;
				$this->allowIp = $result['allowIp'] ;
			} else 
				return $this->returning(null,false,'api4');
		}
		return $this->returning();
	}

	public function search( $searchVariable, $searchWhereClaus , $tableName = 'api'  , $fields = '*' ) {
		$results = \database::searche($tableName, $searchWhereClaus, $searchVariable, true ,false,$fields );
		return $this->returning($results) ;
	}


	public function setApiId( $apiId = null ) {
		$this->apiId = $apiId ;
	}


	public function setName( $name = null ) {
		$this->name = $name ;
	}


	public function setActive( $active = null ) {
		$this->active = $active ;
	}


	public function setHosted( $hosted = null ) {
		$this->hosted = $hosted ;
	}


	public function setAllowIp( $allowIp = null ) {
		$this->allowIp = $allowIp ;
	}


	public function getApiId() {
		return $this->apiId ;
	}


	public function getName() {
		return $this->name ;
	}


	public function getActive() {
		return $this->active ;
	}


	public function getHosted() {
		return $this->hosted ;
	}


	public function getAllowIp() {
		return $this->allowIp ;
	}


	public function insertToDataBase( ) {
		$array['name'] = $this->name ;
		$array['active'] = $this->active ;
		$array['hosted'] = $this->hosted ;
		$array['allowIp'] = $this->allowIp ;
		$id = \database::insert('api' , $array  ); 
		if ( $id ) {
			$this->apiId = $id ; 
			return $this->returning($id) ;
		}
		return $this->returning(null,false,'api3') ;
	}


	public function upDateDataBase( ) {
		$array['name'] = $this->name ;
		$array['active'] = $this->active ;
		$array['hosted'] = $this->hosted ;
		$array['allowIp'] = $this->allowIp ;
		if ( \database::update('api' , $array , array('query' => 'apiId = ?', 'param' => array($this->apiId)) ) ) 
			return $this->returning() ;
		return $this->returning(null,false,'api2') ;
	}


	public function deleteFromDataBase( ) {
		if ( \database::delete('api', array('query' => 'apiId = ?', 'param' => array($this->apiId)) ) ) 
			return $this->returning() ;
		return  $this->returning(null,false,'api1') ;
	}


	public function returnAsArray( ) {
		$array['apiId'] = $this->apiId ;
		$array['name'] = $this->name ;
		$array['active'] = $this->active ;
		$array['hosted'] = $this->hosted ;
		$array['allowIp'] = $this->allowIp ;
		return $array ;
	}



	private function returning($return = null , $status = true , $errorNumber = "api0" , $massagesParams = null ){
		if ( $return == null )
				return $status ;
		else
				return $return ;

	}



}
