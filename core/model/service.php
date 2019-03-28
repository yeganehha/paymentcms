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

class service implements model {


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

	public function __construct(  $searchVariable = null , $searchWhereClaus = 'serviceId = ? ' ){
		if ( $searchVariable != null ) {
			$result = \database::searche('service' ,  $searchWhereClaus  , array($searchVariable) ); 
			if ( $result != null ) {
				$this->serviceId = $result['serviceId'] ;
				$this->link = $result['link'] ;
				$this->price = $result['price'] ;
				$this->description = $result['description'] ;
				$this->name = $result['name'] ;
				$this->status = $result['status'] ;
				$this->lastNameStatus = $result['lastNameStatus'] ;
				$this->firstNameStatus = $result['firstNameStatus'] ;
				$this->emailStatus = $result['emailStatus'] ;
				$this->phoneStatus = $result['phoneStatus'] ;
			} else 
				return $this->returning(null,false,'service4');
		}
		return $this->returning();
	}

	public function search( $searchVariable, $searchWhereClaus , $tableName = 'service'  , $fields = '*' ) {
		$results = \database::searche($tableName, $searchWhereClaus, $searchVariable, true ,false,$fields );
		return $this->returning($results) ;
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


	public function insertToDataBase( ) {
		$array['link'] = $this->link ;
		$array['price'] = $this->price ;
		$array['description'] = $this->description ;
		$array['name'] = $this->name ;
		$array['status'] = $this->status ;
		$array['lastNameStatus'] = $this->lastNameStatus ;
		$array['firstNameStatus'] = $this->firstNameStatus ;
		$array['emailStatus'] = $this->emailStatus ;
		$array['phoneStatus'] = $this->phoneStatus ;
		$id = \database::insert('service' , $array  ); 
		if ( $id ) {
			$this->serviceId = $id ; 
			return $this->returning($id) ;
		}
		return $this->returning(null,false,'service3') ;
	}


	public function upDateDataBase( ) {
		$array['link'] = $this->link ;
		$array['price'] = $this->price ;
		$array['description'] = $this->description ;
		$array['name'] = $this->name ;
		$array['status'] = $this->status ;
		$array['lastNameStatus'] = $this->lastNameStatus ;
		$array['firstNameStatus'] = $this->firstNameStatus ;
		$array['emailStatus'] = $this->emailStatus ;
		$array['phoneStatus'] = $this->phoneStatus ;
		if ( \database::update('service' , $array , array('query' => 'serviceId = ?', 'param' => array($this->serviceId)) ) ) 
			return $this->returning() ;
		return $this->returning(null,false,'service2') ;
	}


	public function deleteFromDataBase( ) {
		if ( \database::delete('service', array('query' => 'serviceId = ?', 'param' => array($this->serviceId)) ) ) 
			return $this->returning() ;
		return  $this->returning(null,false,'service1') ;
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



	private function returning($return = null , $status = true , $errorNumber = "service0" , $massagesParams = null ){
		if ( $return == null )
				return $status ;
		else
				return $return ;

	}



}
