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

class fieldvalue implements model {


	private $fieldId ;
	private $factorId ;
	private $value ;

	public function __construct(  $searchVariable = null , $searchWhereClaus = 'fieldId = ? and factorId = ?' ){
		if ( $searchVariable != null ) {
			$result = \database::searche('fieldvalue' ,  $searchWhereClaus  , $searchVariable );
			if ( $result != null ) {
				$this->fieldId = $result['fieldId'] ;
				$this->factorId = $result['factorId'] ;
				$this->value = $result['value'] ;
			} else
				return $this->returning(null,false,'fieldvalue4');
		}
		return $this->returning();
	}

	public function search( $searchVariable, $searchWhereClaus , $tableName = 'fieldvalue'  , $fields = '*' ) {
		$results = \database::searche($tableName, $searchWhereClaus, $searchVariable, true ,false,$fields );
		return $this->returning($results) ;
	}


	public function setFieldId( $fieldId = null ) {
		$this->fieldId = $fieldId ;
	}


	public function setFactorId( $factorId = null ) {
		$this->factorId = $factorId ;
	}


	public function setValue( $value = null ) {
		$this->value = $value ;
	}


	public function getFieldId() {
		return $this->fieldId ;
	}


	public function getFactorId() {
		return $this->factorId ;
	}


	public function getValue() {
		return $this->value ;
	}


	public function insertToDataBase( ) {
		$array['fieldId'] = $this->fieldId ;
		$array['factorId'] = $this->factorId ;
		$array['value'] = $this->value ;
		$status = \database::insert('fieldvalue' , $array  );
		if ( $status ) {
			return $this->returning() ;
		}
		return $this->returning(null,false,'fieldvalue3') ;
	}


	public function upDateDataBase( ) {
		$array['value'] = $this->value ;
		if ( \database::update('fieldvalue' , $array , array('query' => 'fieldId = ? and factorId = ?', 'param' => array($this->fieldId, $this->factorId)) ) )
			return $this->returning() ;
		return $this->returning(null,false,'fieldvalue2') ;
	}


	public function deleteFromDataBase( ) {
		if ( \database::delete('fieldvalue', array('query' => 'fieldId = ? and factorId = ?', 'param' => array($this->fieldId, $this->factorId)) ) )
			return $this->returning() ;
		return  $this->returning(null,false,'fieldvalue1') ;
	}


	public function returnAsArray( ) {
		$array['fieldId'] = $this->fieldId ;
		$array['factorId'] = $this->factorId ;
		$array['value'] = $this->value ;
		return $array ;
	}



	private function returning($return = null , $status = true , $errorNumber = "fieldvalue0" , $massagesParams = null ){
		if ( $return == null )
				return $status ;
		else
				return $return ;

	}



}
