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
	private $data ;
	private $error = array() ;
	private $returnData ;
	private $tempReturnData ;
	private $isValid ;
	private $methodExploder = '_';

	/**
	 * Validate constructor.
	 *
	 * validation  : required , notEmpty , email , number , numberFormat [ include { } / X 0123456789 + . - ] , maxLen [number] , minLen [number]
 	 * @return boolean
	 *
	 * @param array $data
	 * @param array $validations
	 */
	public function __construct(array  $data, array $validations) {
		$this->isValid = true ;
		$this->returnData = array() ;
		$this->tempReturnData = array() ;
		try {
			$this->data = $data;
			$this->error = null ;
			foreach ($validations as $paramsName => $validationsParameters) {
				$validationsParametersEachOne = null ;
				/*if ( !isset($data[$paramsName]))
					break ;*/

				if ( strpos( $validationsParameters , '|' ) ){
					$validationsParametersEachOne = explode('|',$validationsParameters);
				}
				else {
					$validationsParametersEachOne[] = $validationsParameters ;
				}
				foreach ( $validationsParametersEachOne as $validateType ){
					$validateTypeParameter = null ;
					$strpose = strpos($validateType,':');
					if ( $strpose !== false){
						$validateTypeParameter = substr($validateType , $strpose+1 );
						$validateType = substr($validateType,0,$strpose);
					}
					$validateType = $this->methodExploder.$validateType ;
					if( method_exists($this,$validateType)){
						if ( strpos($paramsName , '.' ) === false ){
							if ( ! isset($data[$paramsName] ))
								break ;
							if ( ! $this->{$validateType}(array($paramsName) , $data[$paramsName] ,$validateTypeParameter ) )
								$this->isValid = false ;
						} else {
							$explode = explode('.' , $paramsName);
							$countExplode = count($explode)  ;
							$tempData = $data;
							$runFunctionFlag = true ;
							for ( $i=0 ; $i < $countExplode ; $i++ ){
								if ( $explode[$i] != '*' ) {
									$tempData = $tempData[$explode[$i]];
									$variableName[$i] = $explode[$i] ;
								} else {
									foreach ( $tempData as $tempDataIndex => $tempDataValue ) {
										$variableName[$i] = $tempDataIndex ;
										$tempData2 = $tempDataValue ;
										for ($iTow = $i + 1; $iTow < $countExplode; $iTow++) {
											if ( isset($tempData2[$explode[$iTow]]))
												$tempData2 = $tempData2[$explode[$iTow]];
											else
												break ;
											$variableName[$iTow] = $explode[$iTow] ;
										}
										if ( ! $this->{$validateType}($variableName , $tempData2 ,$validateTypeParameter ) )
											$this->isValid = false ;
										unset($variableName[$i][$tempDataIndex]);
									}
									$runFunctionFlag = false ;
									$i = $countExplode ;
								}
							}
							if ( $runFunctionFlag )
								if ( ! $this->{$validateType}($variableName , $tempData ,$validateTypeParameter ) )
									$this->isValid = false ;
						}
					} else
						$this->isValid = false ;
				}
			}
		} catch ( \Exception $e) {
			$this->error = $e->getMessage() ;
			$this->isValid = false ;
			return false ;
		}
		return $this->isValid ;
	}


	private function putData($name,$value){
		if ( is_string($name) ){
			$this->returnData[$name] = $value;
		} else {
			$temp = $value ;
			for ( $i = count($name) -1  ; $i >= 0  ; $i-- ){
				$temp2 = $temp ;
				unset($temp);
				$temp[ $name[$i] ] = $temp2 ;
			}
			if ( ! in_array(json_encode($temp) , $this->tempReturnData ) ) {
				$this->returnData = array_merge_recursive($this->returnData,$temp);
				$this->tempReturnData[] = json_encode($temp) ;
			}
		}
		return true ;
	}

	/**
	 * @return string
	 */
	public function getError() {
		return $this->error;
	}

	/**
	 * @return array
	 */
	public function getReturnData() {
		return $this->returnData;
	}

	/**
	 * @return bool
	 */
	public function isValid() {
		return $this->isValid;
	}

	/**
	 * @param $paramsName
	 * @param $paramsValue
	 * @param $paramsValidateType
	 *
	 * @return bool
	 */
	private function _minLen($paramsName , $paramsValue , $paramsValidateType ){
		if ( strlen($paramsValue) < $paramsValidateType ) {
			$this->error[] = array('name' => $paramsName , 'type' => 'minLen' , 'params' => $paramsValidateType );
			return false ;
		}
		$this->putData($paramsName , $paramsValue);
		return true ;
	}

	/**
	 * @param $paramsName
	 * @param $paramsValue
	 * @param $paramsValidateType
	 *
	 * @return bool
	 */
	private function _maxLen($paramsName , $paramsValue , $paramsValidateType ){
		if ( strlen($paramsValue) > $paramsValidateType ) {
			$this->error[] = array('name' => $paramsName , 'type' => 'maxLen' , 'params' => $paramsValidateType );
			return false ;
		}
		$this->putData($paramsName , $paramsValue);
		return true ;
	}

	/**
	 * $paramsValidateType include { } / X 0123456789 + . -
	 * @param $paramsName
	 * @param $paramsValue
	 * @param $paramsValidateType
	 *
	 * @return bool
	 */
	private function _numberFormat($paramsName , $paramsValue , $paramsValidateType ){
		$num_a=array('0','1','2','3','4','5','6','7','8','9');
		$key_a=array('۰','۱','۲','۳','۴','۵','۶','۷','۸','۹');
		$paramsValue = str_replace($key_a,$num_a,$paramsValue);
		if ( $paramsValue == '' ) {
			$this->putData($paramsName , $paramsValue);
			return true;
		}
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
				if ( $return ) {
					$this->putData($paramsName , $paramsValue);
					return true;
				}
			}
		}
		$this->error[] = array('name' => $paramsName, 'type' => 'numberFormat', 'params' => $paramsValidateType);
		return false;
	}


	/**
	 * @param $paramsName
	 * @param $paramsValue
	 * @param $paramsValidateType
	 *
	 * @return bool
	 */
	private function _email($paramsName , $paramsValue , $paramsValidateType ){
		if ( ! filter_var($paramsValue, FILTER_VALIDATE_EMAIL) and $paramsValue != '' ) {
			$this->error[] = array('name' => $paramsName , 'type' => 'email' , 'params' => '' );
			return false ;
		}
		$this->putData($paramsName , $paramsValue);
		return true ;
	}

	/**
	 * @param $paramsName
	 * @param $paramsValue
	 * @param $paramsValidateType
	 *
	 * @return bool
	 */
	private function _required($paramsName , $paramsValue , $paramsValidateType ){
		if ( $paramsValue == null ) {
			$this->error[] = array('name' => $paramsName , 'type' => 'required' , 'params' => '' );
			return false ;
		}
		$this->putData($paramsName , $paramsValue);
		return true ;
	}

	/**
	 * @param $paramsName
	 * @param $paramsValue
	 * @param $paramsValidateType
	 *
	 * @return bool
	 */
	private function _notEmpty($paramsName , $paramsValue , $paramsValidateType ){
		if ( ( ( empty($paramsValue) and $paramsValue != '0' ) or $paramsValue == '' ) or $paramsValue == null  ) {
			$this->error[] = array('name' => $paramsName , 'type' => 'notEmpty' , 'params' => '' );
			return false ;
		}
		$this->putData($paramsName , $paramsValue);
		return true ;
	}

	/**
	 * @param $paramsName
	 * @param $paramsValue
	 * @param $paramsValidateType
	 *
	 * @return bool
	 */
	private function _number($paramsName , $paramsValue , $paramsValidateType ){
		$intParamsValue = intval($paramsValue) + 1 ;
		$intParamsValue = $intParamsValue - 1;
		if ( $intParamsValue !=  $paramsValue ) {
			$this->error[] = array('name' => $paramsName , 'type' => 'notEmpty' , 'params' => '' );
			return false ;
		}
		$this->putData($paramsName , $paramsValue);
		return true ;
	}


}