<?php
/**
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 3/24/2019
 * Time: 7:58 PM
 * project : paymentCMS
 * version : 0.0.0.1
 * update Time : 3/24/2019 - 7:58 PM
 * Description of this Page :
 */


namespace App\model;

if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


interface model {

	public function __construct($searchVariable,$searchWhereClaus);

	/**
	 * @param $searchVariable
	 * @param $searchWhereClaus
	 * @param $tableName
	 * @param $fields
	 *
	 * @return mixed
	 */
	public function search($searchVariable, $searchWhereClaus , $tableName , $fields ) ;
	public function insertToDataBase() ;
	public function upDateDataBase() ;
	public function deleteFromDataBase() ;
	public function returnAsArray() ;
}