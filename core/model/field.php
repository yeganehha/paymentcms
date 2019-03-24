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
	private $lang ;
	private $name ;
	private $title ;
	private $description ;
	private $editable ;
	private $showFront ;
	private $required ;
	private $values ;
	private $regex ;
	private $serviceId ;

	public function __construct(  $searchVariable = null , $searchWhereClaus = 'fieldId = ? ' ){
		if ( $searchVariable != null ) {
			$result = \database::searche('field' ,  $searchWhereClaus  , array($searchVariable) ); 
			if ( $result != null ) {
				$this->fieldId = $result['fieldId'] ;
				$this->lang = $result['lang'] ;
				$this->name = $result['name'] ;
				$this->title = $result['title'] ;
				$this->description = $result['description'] ;
				$this->editable = $result['editable'] ;
				$this->showFront = $result['showFront'] ;
				$this->required = $result['required'] ;
				$this->values = $result['values'] ;
				$this->regex = $result['regex'] ;
				$this->serviceId = $result['serviceId'] ;
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


	public function setLang( $lang = null ) {
		$this->lang = $lang ;
	}


	public function setName( $name = null ) {
		$this->name = $name ;
	}


	public function setTitle( $title = null ) {
		$this->title = $title ;
	}


	public function setDescription( $description = null ) {
		$this->description = $description ;
	}


	public function setEditable( $editable = null ) {
		$this->editable = $editable ;
	}


	public function setShowFront( $showFront = null ) {
		$this->showFront = $showFront ;
	}


	public function setRequired( $required = null ) {
		$this->required = $required ;
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


	public function getFieldId() {
		return $this->fieldId ;
	}


	public function getLang() {
		return $this->lang ;
	}


	public function getName() {
		return $this->name ;
	}


	public function getTitle() {
		return $this->title ;
	}


	public function getDescription() {
		return $this->description ;
	}


	public function getEditable() {
		return $this->editable ;
	}


	public function getShowFront() {
		return $this->showFront ;
	}


	public function getRequired() {
		return $this->required ;
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


	public function insertToDataBase( ) {
		$array['lang'] = $this->lang ;
		$array['name'] = $this->name ;
		$array['title'] = $this->title ;
		$array['description'] = $this->description ;
		$array['editable'] = $this->editable ;
		$array['showFront'] = $this->showFront ;
		$array['required'] = $this->required ;
		$array['values'] = $this->values ;
		$array['regex'] = $this->regex ;
		$array['serviceId'] = $this->serviceId ;
		$id = \database::insert('field' , $array  ); 
		if ( $id ) {
			$this->fieldId = $id ; 
			return $this->returning($id) ;
		}
		return $this->returning(null,false,'field3') ;
	}


	public function upDateDataBase( ) {
		$array['lang'] = $this->lang ;
		$array['name'] = $this->name ;
		$array['title'] = $this->title ;
		$array['description'] = $this->description ;
		$array['editable'] = $this->editable ;
		$array['showFront'] = $this->showFront ;
		$array['required'] = $this->required ;
		$array['values'] = $this->values ;
		$array['regex'] = $this->regex ;
		$array['serviceId'] = $this->serviceId ;
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
		$array['lang'] = $this->lang ;
		$array['name'] = $this->name ;
		$array['title'] = $this->title ;
		$array['description'] = $this->description ;
		$array['editable'] = $this->editable ;
		$array['showFront'] = $this->showFront ;
		$array['required'] = $this->required ;
		$array['values'] = $this->values ;
		$array['regex'] = $this->regex ;
		$array['serviceId'] = $this->serviceId ;
		return $array ;
	}



	private function returning($return = null , $status = true , $errorNumber = "field0" , $massagesParams = null ){
		if ( $return == null )
				return $status ;
		else
				return $return ;

	}



}
