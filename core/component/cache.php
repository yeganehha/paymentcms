<?php


namespace paymentCms\component;


/**
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 4/15/2019
 * Time: 2:47 PM
 * project : paymentCMS
 * virsion : 0.0.0.1
 * update Time : 4/15/2019 - 2:47 PM
 * Discription of this Page :
 */


if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


class cache {

	private static $dataSize = 0;
	private static $name ;
	private static $lifeTime = 0;
	private static $app = 0;
	private static $cacheFilePatch ;
	const version = '0.0.0.1';

	/**
	 * @param           $data
	 * @param           $name
	 * @param float|int $lifeTime
	 * @param null      $app
	 *
	 * @return bool
	 */
	public static function save($data,$name,$lifeTime = 30*24*60*60 , $app = null){
		self::$dataSize = 0 ;
		if ( $data == null or empty($data)) return false;

		if ( is_array($data) )
			self::$dataSize = mb_strlen(serialize($data), '8bit');
		else
			self::$dataSize = mb_strlen($data, '8bit');

		self::$name = $name ;
		self::$lifeTime = $lifeTime ;
		self::$app = $app;
		if ( self::checkDataIsValidOrNo(true) ) return true ;

		return self::writeOnFile($data);
	}

	/**
	 * @param      $name
	 * @param null $app
	 *
	 * @return bool
	 */
	public static function hasLifeTime($name, $app = null){
		self::$name = $name ;
		self::$app = $app;
		return self::checkDataIsValidOrNo() ;
	}

	/**
	 * @param      $name
	 * @param null $data
	 * @param null $app
	 *
	 * @return mixed|string|void
	 */
	public static function get($name, $data = null, $app = null){
		self::$name = $name ;
		self::$app = $app;
		if ( ! self::checkDataIsValidOrNo() ) return ;
		if ( is_null($data) )
			return call_user_func( self::generateFunctionName('storage')) ;
		else {
			$temp = call_user_func( self::generateFunctionName('storage')) ;
			$tempData = self::getVariable($data , $temp );
			if ( $tempData == '__CACHE_SYSTEM_DO_NOT_FIND_RESULT__')
				return ;
			return $tempData ;
		}
	}


	public static function update($name, $data , $value ,$lifeTime = 30*24*60*60 , $app = null){
		self::$name = $name ;
		self::$app = $app;
		if ( ! self::checkDataIsValidOrNo() ) return ;
		if ( is_null($data) )
			return false ;
		else {
			$temp = call_user_func( self::generateFunctionName('storage')) ;
			$result = self::setVariable($data,$value , $temp );
			if ( ! $result )
 				return false;
			return self::save($temp,$name,$lifeTime , $app);
		}
	}

	/**
	 * @param      $name
	 * @param null $app
	 *
	 * @return bool
	 */
	public static function clear($name , $app = null){
		self::$name = $name;
		self::$app = $app ;
		self::getPatchOfFile();
		if ( file_exists(  self::$cacheFilePatch ) )
			if ( unlink(self::$cacheFilePatch) )
				return true;
		return false ;
	}

	private static function getVariable ($find,&$data) {
		if ( ($dotPos = strpos($find,'.')) !== false) {
			$nowFind = substr($find, 0 ,$dotPos);
			$thenFind = substr($find,$dotPos+1);
			if ( $nowFind == '*' ) {
				$return = null ;
				if ( isset($data) and is_array($data)) {
					foreach ( $data as $dataIndex => $dataValue ) {
						if ( $return[$dataIndex] == '__CACHE_SYSTEM_DO_NOT_FIND_RESULT__' )
							break ;
						if ( ($tempValue = self::getVariable($thenFind, $dataValue)) != '__CACHE_SYSTEM_DO_NOT_FIND_RESULT__')
							$return[$dataIndex] = $tempValue ;
					}
				} elseif ( isset($data) and ! is_array($data)) {
					if (isset($data[$nowFind])) {
						return $data[$nowFind];
					} else {
						return '__CACHE_SYSTEM_DO_NOT_FIND_RESULT__';
					}
				} else {
					return '__CACHE_SYSTEM_DO_NOT_FIND_RESULT__';
				}
				return $return;
			} else {
				if (isset($data[$nowFind])) {
					return self::getVariable($thenFind, $data[$nowFind]);
				} else {
					return '__CACHE_SYSTEM_DO_NOT_FIND_RESULT__';
				}
			}
		} else
			if ( isset($data[$find]))
				return $data[$find];
			else
				return '__CACHE_SYSTEM_DO_NOT_FIND_RESULT__';
	}

	private static function setVariable ($find,$newValue,&$data) {
		if ( ($dotPos = strpos($find,'.')) !== false) {
			$nowFind = substr($find, 0 ,$dotPos);
			$thenFind = substr($find,$dotPos+1);
			if ( $nowFind == '*' ) {
				$return = true ;
				if ( isset($data) and is_array($data)) {
					foreach ( $data as $dataIndex => $dataValue ) {
						if ( $return == false )
							break ;
						if ( ($tempValue = self::setVariable($thenFind,$newValue, $dataValue)) == false)
							$return = false ;
					}
				} elseif ( isset($data) and ! is_array($data)) {
					if (isset($data[$nowFind])) {
						$data[$nowFind] = $newValue;
						return true;
					} else {
						return false;
					}
				} else {
					return false;
				}
				return $return;
			} else {
				if (isset($data[$nowFind])) {
					return self::setVariable($thenFind,$newValue, $data[$nowFind]);
				} else {
					return false;
				}
			}
		} else
			if ( isset($data[$find])) {
				$data[$find] = $newValue;
				return true;
			} else
				return false;
	}

	private static function writeOnFile($data){
		try {
			$php = '';
			$php .= "<?php\n";
			$php .= "\t/* cache version " . self::version . " , created at : " . date('Y-m-d H:i:s') . "\n";
			$php .= "\t\t from " . self::$name . " */ \n";
			$php .= "\n\n";
			$php .= "if ( ! function_exists('" . self::generateFunctionName('checker') . "' ) ) {" . "\n\n";
			$php .= "\t" . 'function ' . self::generateFunctionName('checker') . '() {' . "\n";
			$php .= "\t\t" . 'return [ "dataSize" => ' . self::$dataSize . ' , "version" => "' . self::version . '" , "time" => ' . (time() + self::$lifeTime ). ' ]  ;' . "\n";
			$php .= "\t}\n\n";
			$php .= "\t" . 'function ' . self::generateFunctionName('storage') . '() {' . "\n";
			$php .= "\t\treturn " . self::generateDataArrayString('', $data) . " ; \n";
			$php .= "\t}\n\n";
			$php .= "}";
			if (is_file(self::$cacheFilePatch)) unlink(self::$cacheFilePatch);
			File::generate_file(self::$cacheFilePatch, $php);
			return true;
		} catch (\Exception $exception){
			return false ;
		}
	}

	private static function generateDataArrayString($php , $data , $tabCounter = 2){
		if( ! is_array($data)){
			if ( is_string($data) )
				$php = " '".$data."' ";
			else
				$php = " ".$data." ";
		} else {
			$php = " [ \n";
			$tabCounter++;
			foreach ( $data as $index => $value ){
				$php .= str_repeat("\t", $tabCounter) .' "'.$index.'" => '.self::generateDataArrayString($php , $value , $tabCounter) ." , \n";
			}
			$php .= str_repeat("\t", $tabCounter) ." ] ";
		}
		return $php ;
	}

	private static function getPatchOfFile(){
		if ( self::$app == null ){
			$cacheFilePatch = \app::getAppPath('cache\data').DIRECTORY_SEPARATOR;
		} elseif ( self::$app == 'paymentCms' ) {
			$cacheFilePatch = payment_path.'core'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR;
		} else {
			$cacheFilePatch = \app::getAppPath('cache\data' , self::$app).DIRECTORY_SEPARATOR;
		}
		$cacheFilePatch .= md5(self::$name).'.php';
		self::$cacheFilePatch = $cacheFilePatch ;
	}


	private static function checkDataIsValidOrNo($checkDataSize = false ){
		self::getPatchOfFile();
		$functionName = self::generateFunctionName('checker')  ;
		if ( ! function_exists(  $functionName ) ) {
			if ( file_exists(self::$cacheFilePatch) )
				require_once (self::$cacheFilePatch);
			else
				return false ;
		}
		$information =  call_user_func($functionName);
		if ( $information['dataSize'] != self::$dataSize and $checkDataSize )
			return false ;
		if ( $information['version'] != self::version or $information['time'] < time()  )
			return false ;
		return true ;
	}

	private static function generateFunctionName($funcName){
		return 'cacheSystem_'.md5(self::$cacheFilePatch.'_'.self::$name).'_'.$funcName;
	}
}

/*
 * cache::save($nDArray,'test');
 * $result = cache::get('test',2);
 * $result = cache::get('test',"2.1");
 * $result = cache::get('test',"indexItem");
 * $result = cache::get('test',"Items.*.id");
 * $result = cache::get('test',"Items.*.comments.*.id");
 * $result = cache::get('test',"Items.*.*.id");
 * cache::clear('test');
 */