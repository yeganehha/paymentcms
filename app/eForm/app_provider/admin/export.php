<?php


namespace App\eForm\app_provider\admin;


use App\core\controller\fieldService;
use App\core\controller\httpErrorHandler;
use paymentCms\component\httpHeader;
use paymentCms\component\mold\Mold;
use paymentCms\component\Response;

/**
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 6/12/2019
 * Time: 10:47 PM
 * project : paymentCMS
 * virsion : 0.0.0.1
 * update Time : 6/12/2019 - 10:47 PM
 * Discription of this Page :
 */


if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


class export extends \controller {

	public function index($formId = null){

		if ( $formId == null )
			Response::redirect(\App::getBaseAppLink('eForms/all' , 'admin') );

		/* @var \App\eForm\model\eform $form */
		$form = $this->model('eform' , $formId );
		if ( $form->getFormId() != $formId ){
			httpErrorHandler::E404();
			return false ;
		}


		Mold::stopAllAutoCompile();
		$field = fieldService::getFieldsToFillOut($form->getFormId(),'eForm');
		for ( $i = 0 ; $i < count($field['result']) ; $i++ ){
			$data[0][$i] = $field['result'][$i]['fieldId'];
			$data[1][$i] = $field['result'][$i]['title'];
		}
		httpHeader::contentType(' application/csv; charset=UTF-8');
		httpHeader::contentDisposition('"fill_sample_of_form_'.$form->getFormId().'.csv"; filename="fill_sample_of_form_'.$form->getFormId().'.csv"');
		$fp = fopen('php://output', 'wb');
		fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
		foreach ( $data as $line ) {
			$val = implode(",", $line);
			fprintf($fp, $val."\r\n");
		}
		fclose($fp);

	}
}