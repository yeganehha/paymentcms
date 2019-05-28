<?php


namespace App\landing\model;
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

class landingpage extends model implements modelInterFace {

	private $primaryKey = 'landingPageId';
	private $primaryKeyShouldNotInsertOrUpdate = 'landingPageId';
	private $landingPageId ;
	private $name ;
	private $metaDescription ;
	private $template ;
	private $templateName ;
	private $useAsDefault ;

	public function setFromArray($result) {
		$this->landingPageId = $result['landingPageId'] ;
		$this->name = $result['name'] ;
		$this->metaDescription = $result['metaDescription'] ;
		$this->template = $result['template'] ;
		$this->templateName = $result['templateName'] ;
		$this->useAsDefault = $result['useAsDefault'] ;
	}

	public function returnAsArray( ) {
		$array['landingPageId'] = $this->landingPageId ;
		$array['name'] = $this->name ;
		$array['metaDescription'] = $this->metaDescription ;
		$array['template'] = $this->template ;
		$array['templateName'] = $this->templateName ;
		$array['useAsDefault'] = $this->useAsDefault ;
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
	public function getLandingPageId() {
		return $this->landingPageId;
	}

	/**
	 * @param mixed $landingPageId
	 */
	public function setLandingPageId($landingPageId) {
		$this->landingPageId = $landingPageId;
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
	public function getMetaDescription() {
		return $this->metaDescription;
	}

	/**
	 * @param mixed $metaDescription
	 */
	public function setMetaDescription($metaDescription) {
		$this->metaDescription = $metaDescription;
	}

	/**
	 * @return mixed
	 */
	public function getTemplate() {
		return $this->template;
	}

	/**
	 * @param mixed $template
	 */
	public function setTemplate($template) {
		$this->template = $template;
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
	public function getUseAsDefault() {
		return $this->useAsDefault;
	}

	/**
	 * @param mixed $useAsDefault
	 */
	public function setUseAsDefault($useAsDefault) {
		$this->useAsDefault = $useAsDefault;
	}

}
