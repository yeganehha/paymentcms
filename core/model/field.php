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

class field implements model {


	private $fieldId ;
	private $type ;
	private $title ;
	private $description ;
	private $values ;
	private $regex ;
	private $serviceId ;
	private $status ;
	private $orderNumber ;

	public function __construct(  $searchVariable = null , $searchWhereClaus = 'fieldId = ? ' ){
		if ( $searchVariable != null ) {
			$result = \database::searche('field' ,  $searchWhereClaus  , array($searchVariable) ); 
			if ( $result != null ) {
				$this->fieldId = $result['fieldId'] ;
				$this->type = $result['type'] ;
				$this->title = $result['title'] ;
				$this->description = $result['description'] ;
				$this->values = $result['values'] ;
				$this->regex = $result['regex'] ;
				$this->serviceId = $result['serviceId'] ;
				$this->status = $result['status'] ;
				$this->orderNumber = $result['orderNumber'] ;
			} else 
				return $this->returning(null,false,'field4');
		}
		return $this->returning();
	}

	public function search( $searchVariable, $searchWhereClaus , $tableName = 'field'  , $fields = '*' ) {
		$results = \database::searche($tableName, $searchWhereClaus, $searchVariable, true ,false,$fields );
		return $this->returning($results) ;
	}


	public function setFieldId( $fieldId = null ) {
		$this->fieldId = $fieldId ;
	}


	public function setType( $type = null ) {
		$this->type = $type ;
	}


	public function setTitle( $title = null ) {
		$this->title = $title ;
	}


	public function setDescription( $description = null ) {
		$this->description = $description ;
	}


	public function setValues( $values = null ) {
		$this->values = $values ;
	}


	public function setRegex( $regex = null ) {
		$this->regex = $regex ;
	}


	public function setServiceId( $serviceId = null ) {
		$this->serviceId = $serviceId ;
	}


	public function setStatus( $status = null ) {
		$this->status = $status ;
	}


	public function setOrder( $orderNumber = null ) {
		$this->orderNumber = $orderNumber ;
	}


	public function getFieldId() {
		return $this->fieldId ;
	}


	public function getType() {
		return $this->type ;
	}


	public function getTitle() {
		return $this->title ;
	}


	public function getDescription() {
		return $this->description ;
	}


	public function getValues() {
		return $this->values ;
	}


	public function getRegex() {
		return $this->regex ;
	}


	public function getServiceId() {
		return $this->serviceId ;
	}


	public function getStatus() {
		return $this->status ;
	}


	public function getOrder() {
		return $this->orderNumber ;
	}


	public function insertToDataBase( ) {
		$array['type'] = $this->type ;
		$array['title'] = $this->title ;
		$array['description'] = $this->description ;
		$array['values'] = $this->values ;
		$array['regex'] = $this->regex ;
		$array['serviceId'] = $this->serviceId ;
		$array['status'] = $this->status ;
		$array['orderNumber'] = $this->orderNumber ;
		$id = \database::insert('field' , $array  ); 
		if ( $id ) {
			$this->fieldId = $id ; 
			return $this->returning($id) ;
		}
		return $this->returning(null,false,'field3') ;
	}


	public function upDateDataBase( ) {
		$array['type'] = $this->type ;
		$array['title'] = $this->title ;
		$array['description'] = $this->description ;
		$array['values'] = $this->values ;
		$array['regex'] = $this->regex ;
		$array['serviceId'] = $this->serviceId ;
		$array['status'] = $this->status ;
		$array['orderNumber'] = $this->orderNumber ;
		if ( \database::update('field' , $array , array('query' => 'fieldId = ?', 'param' => array($this->fieldId)) ) ) 
			return $this->returning() ;
		return $this->returning(null,false,'field2') ;
	}


	public function deleteFromDataBase( ) {
		if ( \database::delete('field', array('query' => 'fieldId = ?', 'param' => array($this->fieldId)) ) ) 
			return $this->returning() ;
		return  $this->returning(null,false,'field1') ;
	}


	public function returnAsArray( ) {
		$array['fieldId'] = $this->fieldId ;
		$array['type'] = $this->type ;
		$array['title'] = $this->title ;
		$array['description'] = $this->description ;
		$array['values'] = $this->values ;
		$array['regex'] = $this->regex ;
		$array['serviceId'] = $this->serviceId ;
		$array['status'] = $this->status ;
		$array['orderNumber'] = $this->orderNumber ;
		return $array ;
	}



	private function returning($return = null , $status = true , $errorNumber = "field0" , $massagesParams = null ){
		if ( $return == null )
				return $status ;
		else
				return $return ;

	}



}
