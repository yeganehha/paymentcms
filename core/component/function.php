<?php
/**
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 4/23/2019
 * Time: 11:32 AM
 * project : paymentCMS
 * virsion : 0.0.0.1
 * update Time : 4/23/2019 - 11:32 AM
 * Discription of this Page :
 */


if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


function getIP()
{
	if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
	{
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
	{
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}

function getUserAgent()
{
	return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
}

function curl($url,$data){
	$curl_conn = curl_init();
	curl_setopt($curl_conn, CURLOPT_URL, $url);
	curl_setopt($curl_conn, CURLOPT_POST, 1);
	curl_setopt($curl_conn, CURLOPT_POSTFIELDS, $data);
	curl_setopt($curl_conn, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl_conn, CURLOPT_RETURNTRANSFER, 1);
	$output = curl_exec($curl_conn);
	curl_close($curl_conn);
	return $output;
}

function show($pram = null , $exit = true ){
	echo '<pre>';
	var_dump($pram);
	echo '</pre>';
	if ( $exit )
		exit;
}