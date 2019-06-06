<?php
namespace paymentCms\component ;
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

if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');

class Response
{
	public static function redirect($url, $header = true, $exit = true, $sec = 0, $return = false)
	{

		if (!headers_sent() && $header && !$return) {
			httpHeader::redirect($url);
		} else {
			$gre = '<script type="text/javascript"> setTimeout("window.location.href = \'' . str_replace(array('&amp;'), array('&'), $url) . '\'", ' . $sec * 1000 . '); </script>';
			$gre .= '<noscript><meta http-equiv="refresh" content="' . $sec . ';url=' . $url . '" /></noscript>';
			if ($return)
				return $gre;
			echo $gre;
		}
		if ($exit)
			exit;
	}

	public static function json($result, $status = null, $exit = true)
	{
		if (is_null($status)) {
			echo json_encode($result);
		} else {
			echo json_encode(array("status" => $status, "result" => $result));
		}
		httpHeader::contentType('application/json', 'UTF-8');
		if ($exit) exit;
	}

	public static function jsonMessage($message, $status, $result = null, $exit = true)
	{
		self::json(array("status" => $status, "result" => $result, "message" => $message), null, $exit);
	}

	public static function jsonError($message, $statusCode = 400)
	{
		http_response_code($statusCode);
		self::json(['statusCode' => $statusCode, 'message' => $message]);
	}
}