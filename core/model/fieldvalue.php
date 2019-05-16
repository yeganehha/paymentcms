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


use paymentCms\component\model ;

class fieldvalue extends model implements modelInterFace  {

	private $primaryKey = ['fieldId','objectId'];
	private $primaryKeyShouldNotInsertOrUpdate = null;
	private $fieldId ;
	private $objectId ;
	private $objectType ;
	private $value ;


	public function setFromArray($result) {
		$this->fieldId = $result['fieldId'] ;
		$this->objectId = $result['objectId'] ;
		$this->objectType = $result['objectType'] ;
		$this->value = $result['value'] ;
	}

	public function setFieldId( $fieldId = null ) {
		$this->fieldId = $fieldId ;
	}


	public function setObjectId( $objectId = null ) {
		$this->objectId = $objectId ;
	}


	public function setValue( $value = null ) {
		$this->value = $value ;
	}


	public function getFieldId() {
		return $this->fieldId ;
	}


	public function getObjectId() {
		return $this->objectId ;
	}


	public function getValue() {
		return $this->value ;
	}


	public function returnAsArray( ) {
		$array['fieldId'] = $this->fieldId ;
		$array['objectId'] = $this->objectId ;
		$array['objectType'] = $this->objectType ;
		$array['value'] = $this->value ;
		return $array ;
	}


	/**
	 * @return array
	 */
	public function getPrimaryKey() {
		return $this->primaryKey;
	}

	/**
	 * @return null
	 */
	public function getPrimaryKeyShouldNotInsertOrUpdate() {
		return $this->primaryKeyShouldNotInsertOrUpdate;
	}

	/**
	 * @return mixed
	 */
	public function getObjectType() {
		return $this->objectType;
	}

	/**
	 * @param mixed $objectType
	 */
	public function setObjectType($objectType) {
		$this->objectType = $objectType;
	}


}
