<?php

namespace App\paymentServer\app_provider\api;

use App\paymentServer\controller\statistics;
use paymentCms\component\httpHeader;
use paymentCms\component\model;
use paymentCms\component\request;
use paymentCms\component\Response;
use paymentCms\component\validate;


/**
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 4/19/2019
 * Time: 12:21 AM
 * project : paymentCMS
 * virsion : 0.0.0.1
 * update Time : 4/19/2019 - 12:21 AM
 * Discription of this Page :
 */


if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


class report extends \App\api\controller\innerController  {

	public function bug() {
		statistics::addStatistics();
		$data = request::post('file,line,message,type,version,url,app_run,app_provider,controller,method,server,post,get,session,cookie,php' ,null);
		$rules = [
			"file" => ["required", rlang('page')],
			"message" => ["required", rlang('page')],
			"version" => ["required", rlang('page')],
			"url" => ["required", rlang('page')],
			"app_run" => ["required", rlang('page')],
			"controller" => ["required", rlang('page')],
			"method" => ["required", rlang('page')],
			"line" => ["required|match:>0", rlang('page')],
		];
		$valid = validate::check($data, $rules);
		if ($valid->isFail()){
			httpHeader::generateStatusCodeHTTP(200);
			ob_clean();
			exit;
		}
		/* @var  \App\paymentServer\model\bugs $bug */
		$bug = $this->model('bugs' , [$data['file'] , $data['line'], $data['message'] , $data['version']  ] ,' file = ? and line = ? and message = ? and version = ? ');
		if ( $bug->getBugId() == null ){
			$bug->setApp($data['app_run']);
			$bug->setFile($data['file']);
			$bug->setLine($data['line']);
			$bug->setMessage($data['message']);
			$bug->setType($data['type']);
			$bug->setVersion($data['version']);
			$bug->setUrl($data['url']);
			$bug->setAppProvider($data['app_provider']);
			$bug->setController($data['controller']);
			$bug->setMethod($data['method']);
			$data['server'] = json_decode(stripslashes(html_entity_decode($data['server'])),true);
			$data['post'] = json_decode(stripslashes(html_entity_decode($data['post'])),true);
			$data['get'] = json_decode(stripslashes(html_entity_decode($data['get'])),true);
			$data['session'] = json_decode(stripslashes(html_entity_decode($data['session'])),true);
			$data['cookie'] = json_decode(stripslashes(html_entity_decode($data['cookie'])),true);
			$bug->setServer(serialize($data['server']));
			$bug->setPost(serialize($data['post']));
			$bug->setGetData(serialize($data['get']));
			$bug->setSession(serialize($data['session']));
			$bug->setCookie(serialize($data['cookie']));
			$bug->setPhp($data['php']);
			$bug->setFixedVersion(null);
			$bug->setCrashReportTime(date('Y-m-d H:i:s'));
			$bug->insertToDataBase();
		}
		httpHeader::generateStatusCodeHTTP(200);
//		ob_clean();
		exit;
	}

}