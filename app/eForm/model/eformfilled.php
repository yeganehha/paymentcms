<?php


namespace App\eForm\model;


use paymentCms\component\model;
use paymentCms\model\modelInterFace;

/**
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 5/29/2019
 * Time: 5:40 PM
 * project : paymentCMS
 * virsion : 0.0.0.1
 * update Time : 5/29/2019 - 5:40 PM
 * Discription of this Page :
 */


if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


class eformfilled extends model implements modelInterFace{

	private $primaryKey = 'fillId';
	private $primaryKeyShouldNotInsertOrUpdate = 'fillId';
	private $fillId ;
	private $formId ;
	private $userId ;
	private $adminNote ;
	private $fillStart ;
	private $fillEnd ;
	private $ip ;

	public function setFromArray($result) {
		$this->fillId = $result['fillId'] ;
		$this->formId = $result['formId'] ;
		$this->userId = $result['userId'] ;
		$this->adminNote = $result['adminNote'] ;
		$this->fillStart = $result['fillStart'] ;
		$this->fillEnd = $result['fillEnd'] ;
		$this->ip = $result['ip'] ;
	}

	public function returnAsArray( ) {
		$array['formId'] = $this->formId ;
		$array['fillId'] = $this->fillId ;
		$array['userId'] = $this->userId ;
		$array['adminNote'] = $this->adminNote ;
		$array['fillStart'] = $this->fillStart ;
		$array['fillEnd'] = $this->fillEnd ;
		$array['ip'] = $this->ip ;
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
	public function getFillId() {
		return $this->fillId;
	}

	/**
	 * @param mixed $fillId
	 */
	public function setFillId($fillId) {
		$this->fillId = $fillId;
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
	public function getUserId() {
		return $this->userId;
	}

	/**
	 * @param mixed $userId
	 */
	public function setUserId($userId) {
		$this->userId = $userId;
	}

	/**
	 * @return mixed
	 */
	public function getAdminNote() {
		return $this->adminNote;
	}

	/**
	 * @param mixed $adminNote
	 */
	public function setAdminNote($adminNote) {
		$this->adminNote = $adminNote;
	}

	/**
	 * @return mixed
	 */
	public function getFillStart() {
		return $this->fillStart;
	}

	/**
	 * @param mixed $fillStart
	 */
	public function setFillStart($fillStart) {
		$this->fillStart = $fillStart;
	}

	/**
	 * @return mixed
	 */
	public function getFillEnd() {
		return $this->fillEnd;
	}

	/**
	 * @param mixed $fillEnd
	 */
	public function setFillEnd($fillEnd) {
		$this->fillEnd = $fillEnd;
	}

	/**
	 * @return mixed
	 */
	public function getIp() {
		return $this->ip;
	}

	/**
	 * @param mixed $ip
	 */
	public function setIp($ip) {
		$this->ip = $ip;
	}
}