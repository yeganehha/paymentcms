<?php


namespace paymentCms\component;


/**
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 3/23/2019
 * Time: 1:31 PM
 * project : paymentCms
 * virsion : 0.0.0.1
 * update Time : 3/23/2019 - 1:31 PM
 * Discription of this Page :
 */


if (!defined('paymentCms')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


class string {

	// get unique form one string by time,md5,exists,uniqid,...
	public static function get_unique_string($string, $decoding_type = "", $pre = "", $ext = "", $i_loop = "")
	{
		#change it, time..
		if ($decoding_type == "time") {
			list($usec, $sec) = explode(" ", microtime());
			$extra = str_replace('.', '', (float)$usec + (float)$sec);
			$return = $pre . $extra . $i_loop . $ext;
		} # md5
		elseif ($decoding_type == "md5") {
			list($usec, $sec) = explode(" ", microtime());
			$extra = md5(((float)$usec + (float)$sec) . $string);
			$extra = substr($extra, 0, 12);
			$return = $pre . $extra . $i_loop . $ext;
		} # exists before, change it a little
		elseif ($decoding_type == 'exists') {
			$return = $string . '_' . substr(md5(time() . $i_loop), rand(0, 20), 5) . $ext;
			$return = $pre . $return;
		} elseif ($decoding_type == 'uniqid') {
			$return = $pre . uniqid($string) . $i_loop . $ext;
		} #nothing
		else {
			$return = self::changeSignsToOneSing($string) . $ext;
			$return = preg_replace('/-+/', '-', $return);
			$return = $pre . $return;
		}

		return $return;
	}

	// change all Signs To One Sing
	public static function changeSignsToOneSing($string, $sign = '-')
	{
		return preg_replace('/[,.?\/*&^\\\$%#@()_!|"\~\'><=+}{; ]/', $sign, $string);
	}

	// change all english number to persian number
	public static function convertPersianNumbers($matches)
	{
		$farsi_array = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
		$english_array = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');

		return str_replace($english_array, $farsi_array, $matches);
	}

	public static function format($number, $format = 2)
	{
		return number_format((float)$number, $format, '.', '');
	}


	public static function truncateText($text, $chars = 25, $stripTags = false)
	{
		if (empty($text)) return "";
		if ($stripTags) $text = strip_tags($text);

		if (strlen($text) <= $chars) return $text;
		$text = $text . " ";
		$text = substr($text, 0, $chars);
		$text = substr($text, 0, strrpos($text, ' '));
		$text = $text . "...";

		return $text;
	}

	public static function deleteWordLastString($string, $word)
	{
		if (self::strLastHas($string, $word))
		{
			$string = substr($string, 0, strrpos($string, $word));
			$string = (empty($string))? '' : $string;
		}

		return $string;
	}

	public static function strFirstHas($string, $search)
	{
		if (is_array($search)) {
			foreach ($search as $s) {
				if (self::strFirstHas($string, $s)) {
					return true;
				}
			}

		} else {
			if (substr($string, 0, strlen($search)) == $search) {
				return true;
			}
		}

		return false;
	}

	public static function strLastHas($string, $search)
	{
		if (is_array($search)) {
			foreach ($search as $s) {
				if (self::strLastHas($string, $s)) {
					return true;
				}
			}

		} else {
			if (substr($string, -strlen($search)) == $search) {
				return true;
			}
		}
		return false;
	}
	public static function deleteWordFirstString($string, $word)
	{
		if (self::strFirstHas($string, $word))
		{
			$string = substr($string, strlen($word));
			$string = (empty($string))? '' : $string;
		}

		return $string;
	}


	public static function strhas($string, $search)
	{
		if (is_array($search)) {
			foreach ($search as $s) {
				if (self::strhas($string, $s)) {
					return true;
				}
			}

		} else {
			if (strpos($string, $search) !== false) {
				return true;
			}
		}

		return false;
	}
	/*
	 * remove extension in string
	 * @params
	 * $start : min length ext
	 * $end : max length ext
	 */
	public static function deleteExtInString($string, $start = 1, $end = 5)
	{
		return preg_replace('/\\.[^.\\s]{' . $start . ',' . $end . '}$/', '', $string);
	}

	public function existsExt($string, $start = 1, $end = 5)
	{
		return (preg_match('/\\.[^.\\s]{' . $start . ',' . $end . '}$/', $string));
	}

	public static function hideLetters($string, $visibleCount, $replace = '*')
	{
		if (empty($string)) return false;
		$totalCount = strlen($string);
		$hideString = '';
		$counter = $totalCount - $visibleCount;
		while ($counter > 0) {
			$hideString .= $replace;
			$counter--;
		}
		$visible = substr($string, $totalCount - $visibleCount, $totalCount);
		return $hideString . $visible;
	}

	public static function decodeJson($json, $isArray = true)
	{
		return json_decode($json, $isArray);
	}

	public static function encodeJson($json)
	{
		return json_encode($json);
	}

	public static function generateRandomString($length = 10)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	public static function generateRandomLowString($length = 10)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	public static function multiexplode($delimiters, $string)
	{
		$ready = str_replace($delimiters, $delimiters[0], $string);
		$launch = explode($delimiters[0], $ready);
		return $launch;
	}
	public static function camelCase($str,$type='-')
	{
		if(!is_array($type))
			$arr = explode($type,$str);
		else
			$arr = self::multiexplode($type,$str);

		$result = '';
		foreach($arr as $index=>$item)
		{
			$result .= ($index != 0)? ucfirst($item):$item;
		}
		return $result;
	}

	public static function camelToUnderscore($string, $us = "-") {
		return strtolower(preg_replace(
			'/(?<=\d)(?=[A-Za-z])|(?<=[A-Za-z])(?=\d)|(?<=[a-z])(?=[A-Z])/', $us, $string));
	}
}