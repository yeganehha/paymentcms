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


namespace App\Model;

use  App\Model\model;

class contact implements  model {


	private $id ;
	private $lastName ;
	private $firstName ;
	private $phone ;
	private $email ;

	public function __construct(  $searchVariable = null , $searchWhereClaus = 'id = ? ' ){
		if ( $searchVariable != null ) {
			$result = \database::searche('contact' ,  $searchWhereClaus  , array($searchVariable) ); 
			if ( $result != null ) {
				$this->id = $result['id'] ;
				$this->lastName = $result['lastName'] ;
				$this->firstName = $result['firstName'] ;
				$this->phone = $result['phone'] ;
				$this->email = $result['email'] ;
			} else 
				return $this->returning(null,false,'contact4');
		}
		return $this->returning();
	}

	public function search( $searchVariable, $searchWhereClaus , $tableName = 'contact'  , $fields = '*' ) {
		$results = \database::searche($tableName, $searchWhereClaus, $searchVariable, true ,false,$fields );
		return $results ;
	}


	public function setId( $id = null ) {
		$this->id = $id ;
	}


	public function setLastName( $lastName = null ) {
		$this->lastName = $lastName ;
	}


	public function setFirstName( $firstName = null ) {
		$this->firstName = $firstName ;
	}


	public function setPhone( $phone = null ) {
		$this->phone = $phone ;
	}


	public function setEmail( $email = null ) {
		$this->email = $email ;
	}


	public function getId() {
		return $this->id ;
	}


	public function getLastName() {
		return $this->lastName ;
	}


	public function getFirstName() {
		return $this->firstName ;
	}


	public function getPhone() {
		return $this->phone ;
	}


	public function getEmail() {
		return $this->email ;
	}


	public function insertToDataBase( ) {
		$array['lastName'] = $this->lastName ;
		$array['firstName'] = $this->firstName ;
		$array['phone'] = $this->phone ;
		$array['email'] = $this->email ;
		$id = \database::insert('contact' , $array  ); 
		$this->id = $id ; 
		if ( $id ) {
			$this->id = $id ; 
			return $this->returning($id) ;
		}
		return $this->returning(null,false,'contact3') ;
	}


	public function upDateDataBase( ) {
		$array['lastName'] = $this->lastName ;
		$array['firstName'] = $this->firstName ;
		$array['phone'] = $this->phone ;
		$array['email'] = $this->email ;
		if ( \database::update('contact' , $array , array('query' => 'id = ?', 'param' => array($this->id)) ) ) 
			return $this->returning() ;
		return $this->returning(null,false,'contact2') ;
	}


	public function deleteFromDataBase( ) {
		if ( \database::delete('contact', array('query' => 'id = ?', 'param' => array($this->id)) ) ) 
			return $this->returning() ;
		return  $this->returning(null,false,'contact1') ;
	}


	public function returnAsArray( ) {
		$array['id'] = $this->id ;
		$array['lastName'] = $this->lastName ;
		$array['firstName'] = $this->firstName ;
		$array['phone'] = $this->phone ;
		$array['email'] = $this->email ;
		return $array ;
	}



	private function returning($return = null , $status = true , $errorNumber = "contact0" , $massagesParams = null ){
		if ( $return == null )
				return $status ;
		else
				return $return ;

	}



}
