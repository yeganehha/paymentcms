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

class user implements model {


	private $userId ;
	private $fname ;
	private $lname ;
	private $email ;
	private $phone ;

	public function __construct(  $searchVariable = null , $searchWhereClaus = 'userId = ? ' ){
		if ( $searchVariable != null ) {
			$result = \database::searche('user' ,  $searchWhereClaus  , array($searchVariable) ); 
			if ( $result != null ) {
				$this->userId = $result['userId'] ;
				$this->fname = $result['fname'] ;
				$this->lname = $result['lname'] ;
				$this->email = $result['email'] ;
				$this->phone = $result['phone'] ;
			} else 
				return $this->returning(null,false,'user4');
		}
		return $this->returning();
	}

	public function search( $searchVariable, $searchWhereClaus , $tableName = 'user'  , $fields = '*' ) {
		$results = \database::searche($tableName, $searchWhereClaus, $searchVariable, true ,false,$fields );
		return $this->returning($results) ;
	}


	public function setUserId( $userId = null ) {
		$this->userId = $userId ;
	}


	public function setFname( $fname = null ) {
		$this->fname = $fname ;
	}


	public function setLname( $lname = null ) {
		$this->lname = $lname ;
	}


	public function setEmail( $email = null ) {
		$this->email = $email ;
	}


	public function setPhone( $phone = null ) {
		$this->phone = $phone ;
	}


	public function getUserId() {
		return $this->userId ;
	}


	public function getFname() {
		return $this->fname ;
	}


	public function getLname() {
		return $this->lname ;
	}


	public function getEmail() {
		return $this->email ;
	}


	public function getPhone() {
		return $this->phone ;
	}


	public function insertToDataBase( ) {
		$array['fname'] = $this->fname ;
		$array['lname'] = $this->lname ;
		$array['email'] = $this->email ;
		$array['phone'] = $this->phone ;
		$id = \database::insert('user' , $array  ); 
		if ( $id ) {
			$this->userId = $id ; 
			return $this->returning($id) ;
		}
		return $this->returning(null,false,'user3') ;
	}


	public function upDateDataBase( ) {
		$array['fname'] = $this->fname ;
		$array['lname'] = $this->lname ;
		$array['email'] = $this->email ;
		$array['phone'] = $this->phone ;
		if ( \database::update('user' , $array , array('query' => 'userId = ?', 'param' => array($this->userId)) ) ) 
			return $this->returning() ;
		return $this->returning(null,false,'user2') ;
	}


	public function deleteFromDataBase( ) {
		if ( \database::delete('user', array('query' => 'userId = ?', 'param' => array($this->userId)) ) ) 
			return $this->returning() ;
		return  $this->returning(null,false,'user1') ;
	}


	public function returnAsArray( ) {
		$array['userId'] = $this->userId ;
		$array['fname'] = $this->fname ;
		$array['lname'] = $this->lname ;
		$array['email'] = $this->email ;
		$array['phone'] = $this->phone ;
		return $array ;
	}



	private function returning($return = null , $status = true , $errorNumber = "user0" , $massagesParams = null ){
		if ( $return == null )
				return $status ;
		else
				return $return ;

	}



}
