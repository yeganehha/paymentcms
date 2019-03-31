<?php


namespace App\api\controller;


use paymentCms\component\request;

/**
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 3/23/2019
 * Time: 2:05 PM
 * project : paymentCms
 * virsion : 0.0.0.1
 * update Time : 3/23/2019 - 2:05 PM
 * Discription of this Page :
 */


if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


class service extends innerController {


	public function index($serviceId){
		return $this->info($serviceId);
	}

	public function info($serviceId){
		/* @var \paymentCms\model\service $model */
		$model = (new innerController)->model('service',$serviceId);
		if ( is_null($model->getServiceId()) ){
			return self::jsonError('service not found!' ,404 );
		}
		if ( $model->getStatus() != true ){
			return self::jsonError('service not found!' ,403 );
		}
		/* @var \paymentCms\model\field $fieldModel */
		$fieldModel = (new innerController)->model('field') ;
		$fields = $fieldModel->search([$serviceId,'admin' , 'invisible' ],'serviceId = ? and status != ? and status != ? order by orderNumber desc');
		if ( $fields === true)
			$fields = [] ;

		foreach ($fields as $key => $field){
			if ( $field['type'] == 'checkbox' or $field['type'] == 'radio' or $field['type'] == 'select' )
				$fields[$key]['values'] = explode(',', $field['values']);
		}

		return self::json(['service' => $model->returnAsArray() , 'fields' => $fields]);
	}

	public function checkData($serviceId,$baseData = null){
		if ( is_null($baseData) or ! is_array($baseData) )
			$baseData = $_POST;
		$data = request::getFromArray($baseData,'firstName,lastName,email,phone,price,description,customField');
		unset($baseData);
		self::$jsonResponse = false;
		$service = self::info($serviceId);
		self::$jsonResponse = true;
		if ( $service == false and $service == null ){
			return self::jsonError('service not found!' ,404 );
		}
//		show($service);

		function getAddresses($domain) {
			$records = dns_get_record($domain);
			$res = array();
			foreach ($records as $r) {
				if ($r['host'] != $domain) continue; // glue entry
				if (!isset($r['type'])) continue; // DNSSec

				if ($r['type'] == 'A') $res[] = $r['ip'];
				if ($r['type'] == 'AAAA') $res[] = $r['ipv6'];
			}
			return $res;
		}

		function getAddresses_www($domain) {
			$res = getAddresses($domain);
			if (count($res) == 0) {
				$res = getAddresses('www.' . $domain);
			}
			return $res;
		}

		show(getAddresses_www('persionhost.ir') , false);
		/* outputs Array (
		  [0] => 66.11.155.215
		) */
		show(getAddresses_www('google.com'));
		/* outputs Array (
		  [0] => 192.0.43.10
		  [1] => 2001:500:88:200::10
		) */

//		show($_SERVER['REMOTE_ADDR']);
	}
}