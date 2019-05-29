<?php


namespace App\eForm\model;
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




use paymentCms\component\model;
use paymentCms\model\modelInterFace ;

class eform extends model implements modelInterFace {

	private $primaryKey = 'formId';
	private $primaryKeyShouldNotInsertOrUpdate = 'formId';
	private $formId ;
	private $name ;
	private $description ;
	private $lastNote ;
	private $templateName ;
	private $oneTime ;
	private $access ;
	private $published ;
	private $public ;
	private $showHistory ;
	private $password ;

	public function setFromArray($result) {
		$this->formId = $result['formId'] ;
		$this->name = $result['name'] ;
		$this->description = $result['description'] ;
		$this->lastNote = $result['lastNote'] ;
		$this->templateName = $result['templateName'] ;
		$this->oneTime = $result['oneTime'] ;
		$this->access = $result['access'] ;
		$this->published = $result['published'] ;
		$this->public = $result['public'] ;
		$this->showHistory = $result['showHistory'] ;
		$this->password = $result['password'] ;
	}

	public function returnAsArray( ) {
		$array['formId'] = $this->formId ;
		$array['name'] = $this->name ;
		$array['description'] = $this->description ;
		$array['lastNote'] = $this->lastNote ;
		$array['templateName'] = $this->templateName ;
		$array['oneTime'] = $this->oneTime ;
		$array['access'] = $this->access ;
		$array['published'] = $this->published ;
		$array['public'] = $this->public ;
		$array['showHistory'] = $this->showHistory ;
		$array['password'] = $this->password ;
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
	public function getFormId() {
		return $this->formId;
	}

	/**
	 * @param mixed $formId
	 */
	public function setFormId($formId) {
		$this->formId = $formId;
	}

	/**
	 * @return mixed
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param mixed $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @return mixed
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @param mixed $description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * @return mixed
	 */
	public function getLastNote() {
		return $this->lastNote;
	}

	/**
	 * @param mixed $lastNote
	 */
	public function setLastNote($lastNote) {
		$this->lastNote = $lastNote;
	}

	/**
	 * @return mixed
	 */
	public function getTemplateName() {
		return $this->templateName;
	}

	/**
	 * @param mixed $templateName
	 */
	public function setTemplateName($templateName) {
		$this->templateName = $templateName;
	}

	/**
	 * @return mixed
	 */
	public function getOneTime() {
		return $this->oneTime;
	}

	/**
	 * @param mixed $oneTime
	 */
	public function setOneTime($oneTime) {
		$this->oneTime = $oneTime;
	}

	/**
	 * @return mixed
	 */
	public function getAccess() {
		return $this->access;
	}

	/**
	 * @param mixed $access
	 */
	public function setAccess($access) {
		$this->access = $access;
	}

	/**
	 * @return mixed
	 */
	public function getPublished() {
		return $this->published;
	}

	/**
	 * @param mixed $published
	 */
	public function setPublished($published) {
		$this->published = $published;
	}

	/**
	 * @return mixed
	 */
	public function getPublic() {
		return $this->public;
	}

	/**
	 * @param mixed $public
	 */
	public function setPublic($public) {
		$this->public = $public;
	}

	/**
	 * @return mixed
	 */
	public function getShowHistory() {
		return $this->showHistory;
	}

	/**
	 * @param mixed $showHistory
	 */
	public function setShowHistory($showHistory) {
		$this->showHistory = $showHistory;
	}

	/**
	 * @return mixed
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * @param mixed $password
	 */
	public function setPassword($password) {
		$this->password = $password;
	}


}
