<?php


namespace App\paymentServer\controller;


use App\paymentServer\model\plugin_statistics;
use App\paymentServer\model\site_statistics;
use paymentCms\component\model;
use paymentCms\component\request;
use paymentCms\component\strings;

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


class statistics extends \controller {
	public function index(){

	}
	public static function addStatistics(){
		$data = request::post('siteUrl,version,lang,theme,app');
		if ( $data['siteUrl'] == null )
			return ;
		model::transaction();
		/* @var \App\paymentServer\model\site_statistics $site_statics */
		$site_statics = new site_statistics($data['siteUrl']);
		if ( $site_statics->getId() != null ){
			if ( ! ( $site_statics->getListOfDefultLang() == $data['lang'] or strings::strLastHas( $site_statics->getListOfDefultLang(),','.$data['lang']) ) ){
				$site_statics->setListOfDefultLang(  $site_statics->getListOfDefultLang().','.$data['lang'] );
			}
			if ( ! ( $site_statics->getListOfVersion() == $data['version'] or strings::strLastHas( $site_statics->getListOfVersion(),','.$data['version']) ) ){
				$site_statics->setListOfVersion(  $site_statics->getListOfVersion().','.$data['version'] );
			}
		} else {
			$site_statics->setRegisterTime( date('Y-m-d H:i:s') );
			$site_statics->setSiteUrl($data['siteUrl']);
			$site_statics->setListOfVersion(  $data['version'] );
			$site_statics->setListOfDefultLang(  $data['lang'] );
		}
		$site_statics->setLastTimeModify( date('Y-m-d H:i:s') );
		$site_statics->setDefultLang($data['lang']);
		$site_statics->setVersionNumber($data['version']);

		if ( $site_statics->getId() != null ){
			$result = $site_statics->upDateDataBase();
		} else {
			$result = $site_statics->insertToDataBase();
		}
		if ( $result !== false) {
			$data['app'] = stripslashes(html_entity_decode($data['app']));
			$data['app']=json_decode($data['app'],true);
			if (is_array($data['app'])) {
				foreach ($data['app'] as $appName => $app) {
					/* @var \App\paymentServer\model\plugin_statistics $plugin_statics */
					$plugin_statics = new plugin_statistics( [ $site_statics->getId() , $appName] );
					if ( $plugin_statics->getSiteId() != null ){
						$plugin_statics->setLastModify(date('Y-m-d H:i:s'));
						$plugin_statics->upDateDataBase();
					} else {
						$plugin_statics->setAuthor($app['author'] . ' [ ' .$app['support'] .' ]');
						$plugin_statics->setPluginName($appName);
						$plugin_statics->setPluginVersion($app['version']);
						$plugin_statics->setUseing(1);
						$plugin_statics->setInstalled(1);
						$plugin_statics->setSiteId( $site_statics->getId());
						$plugin_statics->setLastModify(date('Y-m-d H:i:s'));
						$plugin_statics->insertToDataBase();
					}
				}
			}
		}
		model::commit();
	}
}