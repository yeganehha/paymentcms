<?php


namespace App\eForm\model;


use App\core\controller\fieldService;
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

	
	public function summery($formId , $startTime = null , $endTime = null , $customField = null){
		if ( $startTime != null  and $endTime == null ) {
			$value[] = $startTime ;
			$variable = ' eformfilled.fillEnd >= ? ' ;
		} elseif ( $endTime != null and $startTime == null) {
			$value[] = $endTime ;
			$variable = ' eformfilled.fillEnd <= ? ' ;
		} elseif ($endTime != null and $startTime != null) {
			$value[] = $startTime ;
			$value[] = $endTime ;
			$variable = ' ( eformfilled.fillEnd BETWEEN ? And ? ) ' ;
		}

		try {
			if ( fieldService::saveInTable() ) {
				$db = parent::db();
				$configDataBase = require payment_path. 'core'.DIRECTORY_SEPARATOR. 'config.php';

				$db->join('field field', ' ( field.serviceId = eformfilled.formId and field.serviceType = "eForm" ) ', "left");
				if (isset($variable))
					$db->Where($variable, $value);
				$db->Where('eformfilled.formId', $formId);
				$db->orderBy("field.orderNumber", "Desc");
				$db->groupBy('field.fieldId');
				$fields = $db->get("eformfilled eformfilled", null, [ 'field.fieldId', 'field.title', 'field.orderNumber', 'field.type , eformfilled.formId']);
				$searchQuery = "";
				if ($customField != null and is_array($customField)) {
					$variable = [] ;
					foreach ($customField as $idCustomField => $valueCustomField) {
						if ($valueCustomField != null or $valueCustomField != '') {
							$variable[] = ' f_'.$idCustomField.' Like "%'.$valueCustomField.'%" ';
						}
					}
					if ( count($variable) > 0 )
					$searchQuery = " WHERE ( ".implode( ' and ' , $variable ) .' ) ';
				}
				$querys = [];
				for ( $i = 0 ; $i < count( $fields ) ; $i++ ){
					$querys[] = "SELECT '".$fields[$i]['fieldId']."' AS fieldId, '".$fields[$i]['title']."' AS title, '".$fields[$i]['orderNumber']."' AS orderNumber, '".$fields[$i]['type']."' AS type, f_".$fields[$i]['fieldId']." AS value, COUNT(f_".$fields[$i]['fieldId'].") AS co FROM ".$configDataBase['_dbTableStartWith']."customFieldValue_".$fields[$i]['formId']."_eForm ".$searchQuery." GROUP BY f_".$fields[$i]['fieldId'] ;
				}
				if ( count($querys) == 0 )
					return null ;
				$query = 'select * From ( '. implode(' UNION ' , $querys) .' ) as t  ORDER BY orderNumber , co DESC ';
				return model::rawQuery($query);
			}
			else {
				$db = parent::db();
				$db->join('field field', 'field.fieldId = fieldvalue.fieldId', "left");
				$db->join('eformfilled eformfilled', ' eformfilled.fillId = fieldvalue.objectId ', 'INNER');
				if (isset($variable)) $db->joinWhere('eformfilled eformfilled', $variable, $value);
				$db->joinWhere('eformfilled eformfilled', 'eformfilled.formId', $formId);
				$db->groupBy('fieldvalue.fieldId,fieldvalue.value');
				$db->orderBy("field.orderNumber", "Desc");
				$db->orderBy("co", "Desc");
				$db->where('fieldvalue.objectType', 'eformfilled');
				if ($customField != null and is_array($customField)) {
					$ids = $db->subQuery();
					foreach ($customField as $idCustomField => $valueCustomField) {
						if ($valueCustomField != null or $valueCustomField != '') {
							$ids->where('( fieldvalue.fieldId = ? and fieldvalue.value LIKE ? )', [$idCustomField, '%' . $valueCustomField . '%']);
						}
					}
					$ids->where('fieldvalue.objectType', 'eformfilled');
					$ids->groupBy('fieldvalue.objectId');
					$ids->get("fieldvalue fieldvalue", null, "fieldvalue.objectId");
					$db->where("fieldvalue.objectId", $ids, 'in');
				}
				return $db->get("fieldvalue fieldvalue", null, ['fieldvalue.value', 'fieldvalue.fieldId', 'field.title', 'field.orderNumber', 'field.type', 'count(*) as co']);
			}
		} catch (\Exception $e) {
			show($e);
			return false ;
		}
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