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

class field extends model implements modelInterFace  {


	private $primaryKey = 'fieldId';
	private $primaryKeyShouldNotInsertOrUpdate = 'fieldId';
	private $fieldId ;
	private $type ;
	private $title ;
	private $description ;
	private $values ;
	private $regex ;
	private $serviceId ;
	private $serviceType ;
	private $status ;
	private $orderNumber ;

	public function setFromArray($result) {
		$this->fieldId = $result['fieldId'] ;
		$this->type = $result['type'] ;
		$this->title = $result['title'] ;
		$this->description = $result['description'] ;
		$this->values = $result['values'] ;
		$this->regex = $result['regex'] ;
		$this->serviceId = $result['serviceId'] ;
		$this->serviceType = $result['serviceType'] ;
		$this->status = $result['status'] ;
		$this->orderNumber = $result['orderNumber'] ;
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


	public function returnAsArray( ) {
		$array['fieldId'] = $this->fieldId ;
		$array['type'] = $this->type ;
		$array['title'] = $this->title ;
		$array['description'] = $this->description ;
		$array['values'] = $this->values ;
		$array['regex'] = $this->regex ;
		$array['serviceId'] = $this->serviceId ;
		$array['serviceType'] = $this->serviceType ;
		$array['status'] = $this->status ;
		$array['orderNumber'] = $this->orderNumber ;
		return $array ;
	}


	/**
	 * @return string
	 */
	public function getPrimaryKey() {
		return $this->primaryKey;
	}

	/**
	 * @return string
	 */
	public function getPrimaryKeyShouldNotInsertOrUpdate() {
		return $this->primaryKeyShouldNotInsertOrUpdate;
	}

	/**
	 * @return mixed
	 */
	public function getServiceType() {
		return $this->serviceType;
	}

	/**
	 * @param mixed $serviceType
	 */
	public function setServiceType($serviceType) {
		$this->serviceType = $serviceType;
	}


}
