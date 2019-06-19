<?php


namespace App\eForm\app_provider\admin;


use App\core\controller\fieldService;
use App\core\controller\httpErrorHandler;
use App\eForm\model\eform;
use App\eForm\model\eformfilled;
use App\user\app_provider\api\user;
use http\Header;
use paymentCms\component\arrays;
use paymentCms\component\file;
use paymentCms\component\httpHeader;
use paymentCms\component\model;
use paymentCms\component\mold\Mold;
use paymentCms\component\request;
use paymentCms\component\Response;
use paymentCms\component\security;
use paymentCms\component\session;
use paymentCms\component\strings;
use paymentCms\component\validate;
use paymentCms\model\field;

/**
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 3/24/2019
 * Time: 10:15 AM
 * project : paymentCMS
 * virsion : 0.0.0.1
 * update Time : 3/24/2019 - 10:15 AM
 * Discription of this Page :
 */


if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


class eFormsAnswer extends \controller {
	public function index($formId = null){
		$this->lists($formId);
	}
	public function lists($formId = null , $userId = null) {
//		show('ssssss');
		if ( request::isFile('importFile') ) {
			$this->import($formId);
		}
		$get = request::post('page=1,perEachPage=25,fname,lname,phone,email,StartTime,EndTime,customField' ,null);
		$rules = [
			"page" => ["required|match:>0", rlang('page')],
			"perEachPage" => ["required|match:>0|match:<501", rlang('page')],
		];
		$valid = validate::check($get, $rules);
		$value = array( );
		$variable = array( );
		$cfvValue = array( );
		$cfvVariable = array( );
		if ($valid->isFail()){
			//TODO:: add error is not valid data

		} else {
			if ( $get['customField'] != null and is_array($get['customField'])) {
				foreach ($get['customField'] as $idCustomField => $valueCustomField ){
					if ($valueCustomField != null or $valueCustomField != '') {
						if ( fieldService::saveInTable() ){
							$value[] = '%' . $valueCustomField . '%';
							$cfvVariable[] = '  cfv.f_'.$idCustomField.' LIKE ?  ';
						} else {
							$value[] = $idCustomField;
							$value[] = '%' . $valueCustomField . '%';
							$cfvVariable[] = ' ( cfv.fieldId = ? and cfv.value LIKE ? ) ';
						}
					}
				}
			}
			if ( $get['fname'] != null ) {
				$value[] = '%'.$get['fname'].'%' ;
				$variable[] = ' u.fname LIKE ? ' ;
			}
			if ( $get['lname'] != null ) {
				$value[] = '%'.$get['lname'].'%' ;
				$variable[] = ' u.lname LIKE ? ' ;
			}
			if ( $get['email'] != null ) {
				$value[] = '%'.$get['email'].'%' ;
				$variable[] = ' u.email LIKE ? ' ;
			}
			if ( $get['phone'] != null ) {
				$value[] = '%'.$get['phone'].'%' ;
				$variable[] = ' u.phone LIKE ? ' ;
			}
			if ( $get['StartTime'] != null  and $get['EndTime'] == null ) {
				$value[] = date('Y-m-d H:i:s' , $get['StartTime'] / 1000 )  ;
				$variable[] = ' e.fillEnd >= ? ' ;
			} elseif ( $get['EndTime'] != null and $get['StartTime'] == null) {
				$value[] = date('Y-m-d H:i:s' , $get['EndTime'] / 1000 );
				$variable[] = ' e.fillEnd <= ? ' ;
			} elseif ($get['EndTime'] != null and $get['StartTime'] != null) {
				$value[] = date('Y-m-d H:i:s' , $get['StartTime'] / 1000 ) ;
				$value[] = date('Y-m-d H:i:s' , $get['EndTime'] / 1000 );
				$variable[] = ' ( e.fillEnd BETWEEN ? And ? ) ' ;
			}
		}
		if ( $formId != null ){
			$value[] = $formId ;
			$variable[] = ' e.formId = ? ' ;
		}
		if ( $userId != null ){
			$value[] = $userId ;
			$variable[] = ' e.userId = ? ' ;
		}
		/* @var eform $model */
		$model = parent::model('eformfilled');
		$model = parent::model('eformfilled');
		model::join('user u' , 'e.userId = u.userId', "left" );
		if ( count($cfvVariable) > 0 ) {
			if ( fieldService::saveInTable() )
				model::join('customFieldValue_'.$formId.'_eForm cfv', ' ( e.fillId = cfv.objectId and cfv.objectType = "eformfilled" and (' . implode(' and ', $cfvVariable) . ') )', "INNER");
			else
				model::join('fieldvalue cfv', ' ( e.fillId = cfv.objectId and cfv.objectType = "eformfilled" and (' . implode(' or ', $cfvVariable) . ') )', "INNER");
		}
		$numberOfAll = ($model->search( (array) $value  , ( count($variable) == 0 ) ? null : implode(' and ' , $variable) , 'eformfilled e', 'COUNT(e.fillId) as co' , null,null)) [0]['co'];
		$pagination = parent::pagination($numberOfAll,$get['page'],$get['perEachPage']);
		model::join('user u' , 'e.userId = u.userId', "left" );
		model::join('eform form' , 'e.formId = form.formId', "left" );
		if ( count($cfvVariable) > 0 ) {
			if (fieldService::saveInTable())
				model::join('customFieldValue_' . $formId . '_eForm cfv', ' ( e.fillId = cfv.objectId and cfv.objectType = "eformfilled" and (' . implode(' and ', $cfvVariable) . ') )', "INNER");
			else
				model::join('fieldvalue cfv', ' ( e.fillId = cfv.objectId and cfv.objectType = "eformfilled" and (' . implode(' or ', $cfvVariable) . ') )', "INNER");
		}
		$search = $model->search( (array) $value  , ( ( count($variable) == 0 ) ? null : implode(' and ' , $variable) )  , 'eformfilled e', 'e.fillId,e.formId,form.name,u.lname,u.userId,u.fname,u.phone,u.email,e.fillEnd'  , ['column' => 'e.fillId' , 'type' =>'desc'] , [$pagination['start'] , $pagination['limit'] ] ,'e.fillId' );
		$fields = fieldService::getFieldsToFillOut($formId,'eForm' );
		$this->mold->set('fields' , $fields['result']);
		$this->mold->path('default', 'eForm');
		$this->mold->view('listOfFormAnswer.mold.html');
		$this->mold->setPageTitle(rlang(['answers' , 'eForm']));
		$this->mold->set('answers' , $search);
		$this->mold->set('formId' , $formId);
		$this->mold->set('admin' , true);
	}
	public function summery($formId = null ) {
		$get = request::post('StartTime,EndTime,customField' ,null);
		/* @var eformfilled $model */
		$model = $this->model('eformfilled');
		$EndTime = null ;
		$startTime = null ;
		if ( $get['StartTime'] != null )
			$startTime = date('Y-m-d H:i:s' , $get['StartTime'] / 1000 ) ;
		if ( $get['EndTime'] != null )
			$EndTime = date('Y-m-d H:i:s' , $get['EndTime'] / 1000 ) ;
		$search = $model->summery($formId,$startTime,$EndTime,$get['customField']);
		usort($search, function($a, $b) {
			return $a['fieldId'] - $b['fieldId'];
		});
//		show($search);
		if ( count($search) ==  0 )
			$search = null;
		$fields = fieldService::getFieldsToFillOut($formId,'eForm' );
		$this->mold->set('fields' , $fields['result']);
		$this->mold->path('default', 'eForm');
		$this->mold->view('summery.mold.html');
		$this->mold->setPageTitle(rlang(['answers' , 'eForm']));
		$this->mold->set('answers' , $search);
	}
	public function yourAnswer() {
		$userId = session::get('userAppLoginInformation')['userId'];
		if ( $userId > 0 ) {
			$this->lists(null, $userId);
			$this->mold->set('activeMenu','userAnswer');
			$this->mold->set('user' , true);
		} else{
			httpErrorHandler::E403();
		}
	}
	public function answer($answerId){

		/* @var \App\eForm\model\eformfilled $answer */
		$answer = $this->model('eformfilled' , $answerId);
		if ( $answer->getFillId() == null ){
			$this->mold->offAutoCompile();
			\App\core\controller\httpErrorHandler::E404();
			return ;
		}
		$user = user::getUser($answer->getUserId(),' userId = ? ');
		/* @var \App\eForm\model\eform $answer */
		$form = $this->model('eform' , $answer->getFormId());

		$allFields = fieldService::showFilledOutFormWithAllFields($answer->getFormId(),'eForm',$answer->getFillId() , 'eformfilled');

//				$this->mold->offAutoCompile();
//				show($answer->returnAsArray(),false);
//				show($user->returnAsArray(),false);
//				show($form->returnAsArray());
		$this->mold->set('answer' , $answer);
		$this->mold->set('form' , $form);
		$this->mold->set('user' , $user);
		$this->mold->set('allFields' , $allFields['result']);
		$this->mold->path('default', 'eForm');
		$this->mold->view('answer.mold.html');
		$this->mold->setPageTitle(rlang('answer'));
	}
	private function import($formId ){
		/* @var \App\eForm\model\eform $form */
		$form = $this->model('eform' , $formId );
		if ( $form->getFormId() != $formId ){
			return false ;
		}


		$target_folder = payment_path.'app'.DIRECTORY_SEPARATOR.'eForm'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR ;
		file::make_folder($target_folder,true);
		$target_file = $target_folder.md5(time().$formId.rand(999,9999)).'.csv' ;
		$file = request::file('importFile');
		if ( move_uploaded_file($file["tmp_name"], $target_file) ){
			$csv = $this->parse_csv_file($target_file);
			model::transaction();
			$error = false;
			foreach ( $csv as $indexRow => $row ){
				if ( $error !== false )
					break ;
				/* @var \App\eForm\model\eformfilled $fill */
				$fill = $this->model('eformfilled' );
				$fill->setUserId( session::get('userAppLoginInformation')['userId'] );
				$fill->setFormId($form->getFormId());
				$fill->setFillStart(date('Y-m-d H:i:s'));
				$fill->setFillEnd(date('Y-m-d H:i:s'));
				$fill->setIp(security::getIp());
				$fill->insertToDataBase();
				if ( $fill->getFillId() > 0 ){
					$resultFillOutForm = fieldService::fillOutForm($form->getFormId(),'eForm',$row, $fill->getFillId() , 'eformfilled');
					if ( ! $resultFillOutForm['status'] )
						$error = $resultFillOutForm['massage'];
				} else
					$error = rlang('pleaseTryAGain');
			}
			if ( $error === false ){
				model::commit();
				unlink($target_file);
				$this->alert('success','',rlang('importDone'));
				return true;
			} else {
				model::rollback();
				unlink($target_file);
				$this->alert('danger','',$error);
				return false;
			}
		} else {
			$this->alert('danger','',rlang('cantUploadFile'));
			return false;
		}
	}

	private function parse_csv_file($csvfile , $firstLineHeader = true) {
		$csv = Array();
		$rowcount = 0;
		if (($handle = fopen($csvfile, "r")) !== FALSE) {
			$max_line_length = defined('MAX_LINE_LENGTH') ? MAX_LINE_LENGTH : 500;
			$header = fgetcsv($handle, $max_line_length);
			$header_colcount = count($header);
			while (($row = fgetcsv($handle, $max_line_length)) !== FALSE) {
				$row_colcount = count($row);
				if ($row_colcount == $header_colcount) {
					if ( $firstLineHeader ){
						for ( $i = 0 ; $i < $header_colcount ; $i ++ ){
							if ( isset($header[$i]) and isset($row[$i]) )
								$csv[$rowcount][ (int) filter_var($header[$i], FILTER_SANITIZE_NUMBER_INT) ] = $row[$i];
						}
					} else
						$csv[] = $row ;
				}
				else {
					return null;
				}
				$rowcount++;
			}
			fclose($handle);
		}
		else {
			return null;
		}
		return $csv;
	}
}