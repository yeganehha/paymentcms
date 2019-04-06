<?php


namespace App\admin\controller;


use mysql_xdevapi\Exception;
use paymentCms\component\request;
use paymentCms\component\Response;
use paymentCms\component\strings;
use paymentCms\component\validate;

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


class webservice extends \controller {
	public function index(){
		Response::redirect(\app::getBaseAppLink('webservice/lists'));
	}
	public function lists( $action = null ) {
		/* @var \paymentCms\model\api $model */
		$model = parent::model('api');
		$apis = $model->search(array(), ' 1 order by apiId desc' );
		if ( $apis === true )
			$apis = null ;

		if ( $action == 'updateActionDone' ){
			$this->mold->set('activeTab','edit');
			$this->alert('success',null,rlang(['edit','webservice','successfully','was']));
		} elseif ( $action == 'insertActionDone' ){
			$this->mold->set('activeTab','dashboard');
			$this->alert('success',null,rlang(['add','webservice','successfully','was']));
		}

		$this->mold->set('apis',$apis);
		$this->mold->view('webserviceList.mold.html');
		$this->mold->setPageTitle(rlang('webservice'));
		$this->mold->set('activeMenu' , 'webservice');
	}


	public function new(){
		if ( request::isPost() ){
			$this->checkData();
		}
		$this->mold->set('newWebservice',true);
		$this->mold->view('webserviceEdit.mold.html');
		$this->mold->setPageTitle(rlang(['add','webservice']));
	}

	private function checkData(){
		$form = request::post('id=0,name,link,ips,active');
		$rules = [
			'id' => ['int|match:>=0'	, rlang('id')],
			'name' => ['required'	, rlang('name')],
			'link' => ['required|url'	, rlang('link')],
		];
		$valid = validate::check($form, $rules);
		if ($valid->isFail()){
			$this->alert('warning' , null,$valid->errorsIn(),'error');
			$this->mold->set('post',$form);
		} else {
			$form['link'] = $this->getDomain($form['link']);
			$ipsInsert = explode(',' , $form['ips']);
			$ipsOfThisDomain = self::getIp($form['link']);
			$ips =  array_filter(array_unique (array_merge ($ipsInsert, $ipsOfThisDomain)));
			$ip = implode(',' , $ips);
			$validateIps = validate::check(['ip' => $ip],['ip' => ['required'	, rlang('ip')]]);
			if ($validateIps->isFail()){
				$this->alert('warning' , null,$validateIps->errorsIn(),'error');
				$this->mold->set('post',$form);
			} else {
				/* @var \paymentCms\model\api $model */
				if ($form['id'] > 0) $model = $this->model('api', $form['id']); else
					$model = $this->model('api');
				$model->setName($form['name']);
				$model->setDomain($form['link']);
				$model->setAllowIp($ip);
				if ($form['active'] == 'active') $model->setActive(true); else
					$model->setActive(false);
				$status = false;
				if ($form['id'] == 0) {
					$action = 'insert';
					$id = $model->insertToDataBase();
					if ($id > 0) $status = true;
				} else {
					$status = $model->upDateDataBase();
					$action = 'update';
				}
				if ($status) {
					Response::redirect(\app::getBaseAppLink('webservice/lists/' . $action . 'ActionDone'));
				} else {
					$this->alert('warning', null, rlang('pleaseTryAGain'), 'error');
					$this->mold->set('post', $form);
				}
			}
		}
	}
	public static function getIp($domain){
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
		$domains = self::getDomain($domain,true,true);
		try {
			$res = getAddresses($domains);
			if (count($res) == 0) {
				$res = getAddresses('www.' . $domains);
			}
			return $res;
		} catch (Exception $exception){
			return [] ;
		}
	}

	private static function getDomain($domain , $removeHttp = false , $removeWww = false){
		$hasHttps =  strings::strFirstHas($domain,'https://') ;
		$hasHttp =  strings::strFirstHas($domain,'http://') ;
		if ( $removeHttp ){
			$domain = strings::deleteWordFirstString($domain,'https://');
			$domain = strings::deleteWordFirstString($domain,'http://');
		}
		if ( $removeWww ){
			$domain = strings::deleteWordFirstString($domain,'www.');
		}
		if ( ! $removeHttp and $removeWww ){
			if ( $hasHttps )
				$domain = 'https://'.strings::deleteWordFirstString($domain,'https://www.');
			elseif ( $hasHttp )
				$domain = 'http://'.strings::deleteWordFirstString($domain,'http://www.');

		}
		$domainTemp = explode('/',$domain);
		if ( ( $hasHttp or $hasHttps ) and ! $removeHttp)
			return $domainTemp[0].'//'.$domainTemp[2];
		else
			return $domainTemp[0];
	}
	public  function edit($webserviceId ){
		/* @var \paymentCms\model\api $model */
		$model = $this->model('api' , $webserviceId) ;
		if ( $model->getApiId() != $webserviceId){
			$this->mold->offAutoCompile();
			\App\core\controller\httpErrorHandler::E404();
			return ;
		}
		if ( request::isPost() ){
			$_POST['id'] = $webserviceId ;
			$this->checkData();
		}
		$this->mold->set('api',$model);
		$this->mold->view('webserviceEdit.mold.html');
		$this->mold->setPageTitle(rlang(['profile','webservice']). ': '.$model->getName());
	}

}