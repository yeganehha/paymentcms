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


namespace App\paymentServer\model;

use paymentCms\component\model;
use paymentCms\model\modelInterFace ;

class plugin_list extends model implements modelInterFace {

	private $primaryKey = 'name';
	private $primaryKeyShouldNotInsertOrUpdate = null;
	private $name ;
	private $version ;
	private $author ;
	private $icon ;

	public function setFromArray($result) {
		$this->name = $result['name'] ;
		$this->version = $result['version'] ;
		$this->author = $result['author'] ;
		$this->icon = $result['icon'] ;
	}


	public function returnAsArray( ) {
		$array['name'] = $this->name ;
		$array['version'] = $this->version ;
		$array['author'] = $this->author ;
		$array['icon'] = $this->icon ;
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
	public function getAuthor() {
		return $this->author;
	}

	/**
	 * @param mixed $author
	 */
	public function setAuthor($author) {
		$this->author = $author;
	}

	/**
	 * @return mixed
	 */
	public function getIcon() {
		return $this->icon;
	}

	/**
	 * @param mixed $icon
	 */
	public function setIcon($icon) {
		$this->icon = $icon;
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
	public function getVersion() {
		return $this->version;
	}

	/**
	 * @param mixed $version
	 */
	public function setVersion($version) {
		$this->version = $version;
	}


}
