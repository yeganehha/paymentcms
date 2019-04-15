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

	private $primaryKey = ['fieldId','invoiceId'];
	private $primaryKeyShouldNotInsertOrUpdate = null;
	private $fieldId ;
	private $invoiceId ;
	private $value ;


	public function setFromArray($result) {
		$this->fieldId = $result['fieldId'] ;
		$this->invoiceId = $result['invoiceId'] ;
		$this->value = $result['value'] ;
	}

	public function setFieldId( $fieldId = null ) {
		$this->fieldId = $fieldId ;
	}


	public function setInvoiceId( $invoiceId = null ) {
		$this->invoiceId = $invoiceId ;
	}


	public function setValue( $value = null ) {
		$this->value = $value ;
	}


	public function getFieldId() {
		return $this->fieldId ;
	}


	public function getInvoiceId() {
		return $this->invoiceId ;
	}


	public function getValue() {
		return $this->value ;
	}


	public function returnAsArray( ) {
		$array['fieldId'] = $this->fieldId ;
		$array['invoiceId'] = $this->invoiceId ;
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


}
