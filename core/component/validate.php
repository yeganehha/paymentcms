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


class validate {
	
	private static $obj;
	private static $inputs;
	private static $rules;
	private static $customMessages;
	private static $listMethodValidators;
	private static $errors;
	private static $isFail;
	private static $data;
	private static $field_title;
	private static $currentKey;
	private static $isNot = false;
	private static $activeMethod;
	private static $isErr = false;
	private static $resultMethod = true;
	private static $params = [];
	private static $validators = [];
	private static $field_titles = [];
	private static $field_types = [];

	/**
	 *
	 * @return \paymentCms\component\validate
	 */
	public static function check($inputs, $rules, $messages = null)
	{
		self::$isErr = true;
		self::$inputs = $inputs;
		self::$rules = $rules;
		self::$customMessages = $messages;

		self::init();
		self::validateInputsByRules();

		return self::$obj;
	}

	private static function init()
	{
		if (empty(self::$obj))
			self::$obj = new validate();
		self::getDefinedValidators();
		self::setFail(false);
	}

	private static function getDefinedValidators()
	{
		if (!empty(self::$listMethodValidators)) return;

		$class = new \ReflectionClass(self::class);
		$methods = $class->getMethods(
			\ReflectionMethod::IS_PUBLIC |
			\ReflectionMethod::IS_PROTECTED |
			\ReflectionMethod::IS_PRIVATE
		);
		foreach ($methods as $method) {
			$mName = $method->name;
			if (substr($mName, 0, 1) == '_') {
				self::$listMethodValidators[] = $mName;
			}
		}
	}

	private static function setFail($status = true)
	{
		self::$isFail = $status;
	}

	private static function validateInputsByRules()
	{
		foreach (self::$rules as $key => $conditions) {
			self::$currentKey = $key;
			if (is_array($conditions)) {
				$part_conds = $conditions[0];
				self::$field_title = $conditions[1];
			} else {
				$part_conds = $conditions;
			}

			self::$field_titles[$key] = self::$field_title;

			self::$data = arrays::searchArrayByPattern($key, self::$inputs);

			$condParts = explode('|', $part_conds);
			self::$field_types[$key] = $condParts;
			foreach ($condParts as $cond) {
				self::executeRuleMethod($cond);
			}
		}
	}

	private static function executeRuleMethod($ruleName)
	{
		$params = array();
		$method = self::getMethodName($ruleName);

		//check for exist parameters
		if (strings::strhas($method, ':')) {
			$parts = explode(':', $method);
			$method = isset($parts[0]) ? $parts[0] : null;
			$params = isset($parts[1]) ? $parts[1] : array();
			//check for multi parameters
			if (!empty($params)) {
				$params = explode(',', $params);
			}
		}
		if (empty($params)) $params = array();

		self::$params = $params;

		if (isset(self::$validators[$method])) {
			$validator = self::$validators[$method];
			self::executeRuleGenerate($validator['status'], $validator['err'], $validator['dataIntoMessage']);
		} else if (self::isValidCountParams(self::class, $method, $params)) {
			self::$activeMethod = $method;
			self::callMethod(self::class, $method, $params);
		} else {
			//give an error...
		}
	}

	private static function getMethodName($ruleName)
	{
		self::$isNot = false;
		$ruleName = trim($ruleName);

		if (strings::strFirstHas($ruleName, '!')) {
			$ruleName = strings::deleteWordFirstString($ruleName, '!');
			self::$isNot = true;
		}

		$method = '_' . $ruleName;

		return $method;
	}

	private static function executeRuleGenerate(\Closure $status, $err, \Closure $dataIntoMessage = null)
	{
		$info = [
			'data' => self::getData(),
			'first_data' => self::getFirstData(),
			'title' => self::$field_title,
			'titles' => self::$field_titles,
			'inputs' => self::$inputs,
		];
		$options = self::$params;
		$status = $status($info, $options);
		if (!empty($dataIntoMessage))
			$dataIntoMessage = $dataIntoMessage($info, $options);
		if (is_array($err)) {
			$message_err = $err[0];
			$not_message_err = $err[1];
			self::setMultiResult($status, $message_err, $not_message_err, $dataIntoMessage);
		} else {
			self::setError($err, $dataIntoMessage);
		}
	}

	private static function getData()
	{
		return self::$data['values'];
	}

	private static function getFirstData()
	{
		$data = self::getData();
		return isset($data[0]) ? $data[0] : null;
	}

	private static function setMultiResult($status, $err, $notErr, $dataToStr = array())
	{
		if ($status && self::$isNot) {
			self::setError($notErr, $dataToStr);
		}

		if (!$status && !self::$isNot) {
			self::setError($err, $dataToStr);
		}
	}

	private static function setError($err = "No Message", $dataToStr = array())
	{
		if (self::$isErr) {
			//use custom message if have been set
			$message = self::customizeMessage();
			if ($message === false || is_null($message))
				$message = arrays::dataToStrArray($err, $dataToStr);

			self::$errors[self::$currentKey][] = $message;
			self::setFail(true);
		}

		self::$resultMethod = false;
	}

	private static function customizeMessage()
	{
		if (empty(self::$customMessages)) return false;

		foreach (self::$customMessages as $ck => $cv) {
			$parts = explode(':', $ck);
			$key = isset($parts[0]) ? $parts[0] : null;
			$rule = isset($parts[1]) ? $parts[1] : null;
			$method = self::getMethodName($rule);

			//if is set for specific rule a custom message
			if (self::$currentKey == $key) {
				if (empty($rule) || $method == self::$activeMethod)
					return $cv;
			}
		}
		return null;
	}

	// emails.*
	// courses.*.seasons.*.lessons.*.title

	private static function isValidCountParams($class, $method, $params)
	{
		$r = new \ReflectionMethod($class, $method);
		$p = $r->getParameters();
		$lengthP = 0;
		$lengthParams = count($params);
		if (count($p) > 0) {
			foreach ($p as $key => $value) {
				if (!$value->isDefaultValueAvailable())
					$lengthP = $key + 1;
			}
		}

		if ($lengthParams >= $lengthP) return true;
		return false;
	}

	private static function callMethod($class, $method, $params)
	{
		if (self::ignore()) return;
		call_user_func_array(array($class, $method), $params);
	}

	private static function ignore()
	{
		$types = isset(self::$field_types[self::$currentKey]) ? self::$field_types[self::$currentKey] : [];
		$required = self::$data['required'];
		if (!in_array('required', $types) && !$required) return true;
		return false;
	}

	public static function checkOne($value, $rule, $patternArray = null)
	{
		self::$resultMethod = true;

		if (!empty($patternArray)) {
			self::$data = arrays::searchArrayByPattern($patternArray, $value);
		} else {
			self::$data['required'] = true;
			self::$data['values'] = [$value];
		}

		$condParts = explode('|', $rule);
		foreach ($condParts as $cond) {
			self::executeRuleMethod($cond);
		}

		return self::$resultMethod;
	}

	public static function isFail()
	{
		return self::$isFail;
	}

	public static function getError()
	{
		$result = [];
		foreach (self::$errors as $err) {
			$result = array_merge($result, array_values($err));
		}

		return $result;
	}

	/**
	 * get first error
	 */
	public static function first($key = null)
	{
		$errors = self::getError();
		if (!empty($errors)) {
			if (is_array($errors)) {
				$err = array_shift($errors);
				if (is_array($err))
					return isset($err[0]) ? $err[0] : null;
				return $err;
			} else {
				return array_shift($errors);
			}
		}

		return array();
	}

	/**
	 * get first error
	 */
	public static function errorsIn()
	{
		$errors = self::getError();
		return implode('<br>',$errors);
	}

	/**
	 * get errors
	 */
	public static function get($key = null)
	{
		if (isset(self::$errors[$key]))
			return self::$errors[$key];

		return self::$errors;
	}

	public static function __callStatic($method, $arguments)
	{
		self::$resultMethod = true;
		$method = '_' . $method;
		if (count($arguments) < 1) return false;
		$data = $arguments[0];
		array_shift($arguments);
		if (isset(self::$validators[$method])) {
			self::$data['required'] = true;
			self::$data['values'] = [$data];

			$validator = self::$validators[$method];
			self::executeRuleGenerate($validator['status'], $validator['err'], $validator['dataIntoMessage']);
		} else if (method_exists(self::class, $method) && self::isValidCountParams(self::class, $method, $arguments)) {

			self::$data['required'] = true;
			self::$data['values'] = [$data];

			self::callMethod(self::class, $method, $arguments);
			self::$data = null;
			return self::$resultMethod;
		}
	}

	// check count params of a method in class

	public static function generate($name, \Closure $status, $err, \Closure $dataIntoMessage = null)
	{
		self::$validators['_' . $name] = [
			'status' => $status,
			'err' => $err,
			'dataIntoMessage' => $dataIntoMessage,
		];
	}

	/**
	 * @param $lengthParams : this params can mix with math operators
	 * example: >=2
	 * with getValueFromParams() can extract value : 2
	 * with getOperatorFromParams() can extract operator : >=
	 */
	private static function _length($lengthParams)
	{
		$data = self::getData();
		$length = self::getValueFromParams($lengthParams);
		$operator = self::getOperatorFromParams($lengthParams);

		if (is_array($data)) {
			foreach ($data as $d) {
				if (is_array($d)) continue;

				$dataLen = strlen($d);
				self::compareLength($dataLen, $length, $d, $operator);
			}
		} else {
			$dataLen = strlen($data);
			self::compareLength($dataLen, $length, $data, $operator);
		}

	}

	/**
	 * this method extract only value without operators
	 * example: input : >= 23
	 *          output: 23
	 */
	private static function getValueFromParams($params)
	{
		return str_replace(self::getOperatorFromParams($params), '', $params);
	}

	/**
	 * this method extract only operator without value
	 * example: input : >= 23
	 *          output: >=
	 */
	private static function getOperatorFromParams($params)
	{
		preg_match('/!=|==|<=|<|>=|>/', $params, $out);
		return isset($out[0]) ? $out[0] : '';
	}

	private static function compareLength($dataLen, $length, $data, $operator)
	{
		switch ($operator) {
			case '==': {
				if ($dataLen != $length) self::setError(rlang('ERROR_VALID_LENGTH_EQUAL'), [self::$field_title, $length]);
				break;
			}
			case '!=': {
				if ($dataLen == $length) self::setError(rlang('ERROR_VALID_LENGTH_NOT_EQUAL'), [self::$field_title, $length]);
				break;
			}
			case '>=': {
				if ($dataLen < $length) self::setError(rlang('ERROR_VALID_LENGTH_GTE'), [self::$field_title, $length]);
				break;
			}
			case '<=': {
				if ($dataLen > $length) self::setError(rlang('ERROR_VALID_LENGTH_LTE'), [self::$field_title, $length]);
				break;
			}
			case '>': {
				if ($dataLen <= $length) self::setError(rlang('ERROR_VALID_LENGTH_GRATER'), [self::$field_title, $length]);
				break;
			}
			case '<': {
				if ($dataLen >= $length) self::setError(rlang('ERROR_VALID_LENGTH_LESSER'), [self::$field_title, $length]);
				break;
			}
		}
	}
	/*========================================= Validators ==========================================
	 * All Rules Come Here...
	 * you can add your custom validators as a method and use it as a rule
	 *
	 * How to Define own validator ?
	 * your method must define as static method and must be start with underline:
	 *  -- example -->  public static function _myRule(){ //do staff }
	 * also rule support arguments
	 */

	private static function _required()
	{
		if (!self::$data['required']) {
			self::setError(rlang('ERROR_VALID_REQUIRED'), self::$field_title);
			return;
		}
		self::$isNot = true;
		self::_empty();
	}

	private static function _empty()
	{
		$value = self::getFirstData();
		if (!is_array($value))
			$value = trim($value);
		$isEmpty = false;
		if (!is_bool($value) && !is_numeric($value) && empty($value))
			$isEmpty = true;

		self::setMultiResult($isEmpty, rlang('ERROR_VALID_EMPTY'), rlang('ERROR_VALID_NOT_EMPTY'), self::$field_title);

	}

	private static function _notEmpty()
	{
		$value = self::getFirstData();
		if (!is_array($value))
			$value = trim($value);
		$isNotEmpty = false;
		if (!is_bool($value) && !is_numeric($value) &&  !empty($value))
			$isNotEmpty = true;

		self::setMultiResult($isNotEmpty, rlang('ERROR_VALID_NOT_EMPTY'), rlang('ERROR_VALID_EMPTY'), self::$field_title);

	}

	private static function _number()
	{
		$value = self::getFirstData() ;
		if (! ( is_numeric($value) or $value ==  "" ) ) {
			self::setError(rlang('ERROR_VALID_NUMBER'), self::$field_title);
		}
	}

	private static function _username()
	{
		if (!preg_match('/^[a-zA-Z]+[_]{0,1}[a-zA-Z0-9]+$/m', self::getFirstData())) {
			self::setError(rlang('ERROR_VALID_USERNAME'), self::$field_title);
		}
	}

	private static function _extension()
	{
		if (!preg_match('/^[a-zA-Z0-9]+[_]{0,1}[a-zA-Z0-9]+$/m', self::getFirstData())) {
			self::setError(rlang('ERROR_VALID_EXTENSION'), self::$field_title);
		}
	}

	private static function _match($field, $fieldName2 = null)
	{
		$operator = self::getOperatorFromParams($field);
		$key = str_replace($operator, '', $field);
		$title2 = $key;
		$checkField = $key;
		if(strings::strFirstHas($key, '[') && strings::strLastHas($key, ']'))
		{
			$key = str_replace(['[', ']'], '', $key);
			$values = arrays::searchArrayByPattern($key, self::$inputs);
			$checkField = isset($values['values'][0]) ? $values['values'][0] : null;
			$title2 = isset(self::$field_titles[$key]) ? self::$field_titles[$key] : $key;
		}

		$fieldName2 = (empty($fieldName2)) ? $title2 : $fieldName2;
		self::compareValue($operator, self::getFirstData(), $checkField, $fieldName2);
	}

	private static function compareValue($operator, $value1, $value2, $fieldName2)
	{
		switch ($operator) {
			case '==': {
				if ($value1 != $value2) self::setError(rlang('ERROR_VALID_VALUE_EQUAL'), [self::$field_title, $fieldName2]);
				break;
			}
			case '!=': {
				if ($value1 == $value2) self::setError(rlang('ERROR_VALID_VALUE_NOT_EQUAL'), [self::$field_title, $fieldName2]);
				break;
			}
			case '>=': {
				if ($value1 < $value2) self::setError(rlang('ERROR_VALID_VALUE_GTE'), [self::$field_title, $fieldName2]);
				break;
			}
			case '<=': {
				if ($value1 > $value2) self::setError(rlang('ERROR_VALID_VALUE_LTE'), [self::$field_title, $fieldName2]);
				break;
			}
			case '>': {
				if ($value1 <= $value2) self::setError(rlang('ERROR_VALID_VALUE_GRATER'), [self::$field_title, $fieldName2]);
				break;
			}
			case '<': {
				if ($value1 >= $value2) self::setError(rlang('ERROR_VALID_VALUE_LESSER'), [self::$field_title, $fieldName2]);
				break;
			}
		}
	}

	private static function _email()
	{
		if (filter_var(self::getFirstData(), FILTER_VALIDATE_EMAIL) === false)
			self::setError(rlang('ERROR_VALID_EMAIL'));
	}

	private static function _url()
	{
		if (filter_var(self::getFirstData(), FILTER_VALIDATE_URL) === false)
			self::setError(rlang('ERROR_VALID_URL'));
	}

	private static function _signs()
	{
		$isSigns = false;
		if (preg_match('/[,.?\/*&^\\\$%#@()_!|"\~\'><=+}{; ]/', self::getFirstData()))
			$isSigns = true;

		self::setMultiResult($isSigns, rlang('ERROR_VALID_SINGS'), rlang('ERROR_VALID_NOT_SINGS'), self::$field_title);
	}

	private static function _name($type)
	{
		$regex = '';
		switch ($type) {
			case 'folder':
				$regex = '/^(?!(?:CON|PRN|AUX|NUL|COM[1-9]|LPT[1-9])(?:\.[^.]*)?$)[^<>:"\\\\|?*\x00-\x1F]*[^<>:"\\\\|?*\x00-\x1F\ .]$/';
				break;
			case 'file':
				$regex = '/^(?!^(PRN|AUX|CLOCK\$|NUL|CON|COM\d|LPT\d|\..*)(\..+)?$)[^\x00-\x1f\\\\?*:\";|><\/]+$/';
				break;
		}

		if (!preg_match($regex, self::getFirstData()))
			self::setError(rlang('ERROR_VALID_NAME'), self::$field_title);

	}

	private static function _int()
	{
		if (filter_var(self::getFirstData(), FILTER_VALIDATE_INT) === false)
			self::setError(rlang('ERROR_VALID_INT'), self::$field_title);
	}

	private static function _mobile()
	{
		$mobile = self::getFirstData();
		if (strlen($mobile) != 11 || substr($mobile, 0, 2) != '09')
			self::setError(rlang('ERROR_VALID_MOBILE'), self::$field_title);
	}

	private static function _float()
	{
		if (filter_var(self::getFirstData(), FILTER_VALIDATE_FLOAT) === false)
			self::setError(rlang('ERROR_INVALID_REQUEST'), self::$field_title);
	}


	private static function _jdate()
	{
		$isResult = false;
		$regex = '/\d{2,4}[-|\/]\d{1,2}[-|\/]\d{1,2}[#]/';
		if (preg_match($regex, self::getFirstData() . '#'))
			$isResult = true;

		$fields = (is_array(self::$field_title)) ? self::$field_title : [self::$field_title, 'YY/MM/DD'];
		self::setMultiResult($isResult, rlang('ERROR_VALID_DATE'), rlang('ERROR_VALID_NOT_DATE'), $fields);
	}


	/**
	 * $paramsValidateType include { } / X 0123456789 + . -
	 * @param $paramsValidateType
	 *
	 * @return bool
	 */
	private static function _format( $paramsValidateType ){
		$paramsName = self::$field_title ;
		$paramsValue = self::getFirstData() ;
		$num_a=array('0','1','2','3','4','5','6','7','8','9');
		$key_a=array('۰','۱','۲','۳','۴','۵','۶','۷','۸','۹');
		$paramsValue = str_replace($key_a,$num_a,$paramsValue);
		$explodeAccolade = preg_split("/{|}/", $paramsValidateType);
		$correctGroupWords = array();
		if ( count($explodeAccolade) >  0 ) {
			foreach ($explodeAccolade as $indexWord => $valueOfWord) {
				if (strpos($valueOfWord, '/') === false) {
					if (count($correctGroupWords) > 0) {
						foreach ($correctGroupWords as $indexOfGroupWord => $valueOfGroupWords) {
							$correctGroupWords[$indexOfGroupWord] .= $valueOfWord;
						}
					} else {
						$correctGroupWords[] = $valueOfWord;
					}
				} else {
					$explodedWord = explode('/', $valueOfWord);
					$newCorrectGroupWords = array();
					foreach ($explodedWord as $numberOfExplodedWord => $oneOfThem) {
						if (count($correctGroupWords) > 0) {
							foreach ($correctGroupWords as $indexOfGroupWord => $valueOfGroupWords) {
								$newCorrectGroupWords[] = $valueOfGroupWords . $oneOfThem;
							}
						} else {
							$newCorrectGroupWords[] = $oneOfThem;
						}
					}
					if (count($correctGroupWords) > 0) {
						foreach ($correctGroupWords as $indexOfGroupWord => $valueOfGroupWords) {
							unset($correctGroupWords[$indexOfGroupWord]);
						}
					}
					$correctGroupWords = array_merge($correctGroupWords, $newCorrectGroupWords);
				}
			}
		} else
			$correctGroupWords[] = $paramsValidateType ;
		usort($correctGroupWords, function($a, $b) {
			return strlen($a) - strlen($b);
		});
		foreach ( $correctGroupWords as $indexOfWord => $correctWord ){
			if ( strlen($paramsValue) == strlen($correctWord)) {
				$return = true ;
				for ($i = 0; $i < strlen($paramsValue); $i++) {
					if ( substr($correctWord,$i,1) != 'X' ) {
						if (substr($paramsValue, $i, 1) != substr($correctWord, $i, 1)) {
							$return = false ;
							break;
						}
					} else {
						if ( ! ( ( intval(substr($paramsValue,2,1)) > 0 and intval(substr($paramsValue,2,1)) < 10 ) or substr($paramsValue,2,1) == '0' ) ){
							$return = false ;
							break;
						}
					}
				}
				if ( $return )
					return true;
			}
		}
		self::setError(rlang('ERROR_INVALID_FORMAT'), self::$field_title);
		return false;
	}



}