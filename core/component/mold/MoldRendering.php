<?php

namespace paymentCms\component\mold;


use paymentCms\component\file;
use paymentCms\component\strings;

/**
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 2/24/2019
 * Time: 3:17 PM
 * project : pinoox
 * virsion : 1.0
 * update Time : 2/24/2019 - 3:17 PM
 * Discription of this Page :
 */
class MoldRendering {

	private $version = null ;
	private $string = null ;
	private $moldData = null ;
	private static $map = [] ;
	private $fileInfo = [] ;
	private $runFromPhp = false ;
	private $moldShouldReplaceInTheEnd  = [] ;
	private $moldForeachElse  = [] ;
	private $engineStatus = true ;
	private $lastEngineStatusFalse = 0 ;
	const startVariable = '$' ;
	const variableArraySeparator = '.' ;
	const parameterSeparator = ',' ;
	const replaceWithEndFunctionName = '/' ;

	public function __construct($string , $fileInfo , &$moldData ,$version , $runFromPhp = false ) {
		$this->string = str_replace( '<?php' , '<?<?php echo \'php\' ; ?>'  ,$string );
		$this->string = str_replace( '<script' , '<<?php echo \'script\' ; ?>' ,$string );
		$this->string = str_replace( '<style' , '<<?php echo \'style\' ; ?>' ,$string );
		$this->fileInfo = $fileInfo ;
		$this->runFromPhp = $runFromPhp ;
		$this->moldData = $moldData ;
		$this->version = $version ;
		if (! empty($string) ) {
			$this->explode();
		}
	}

	public static function emptyMap(){
		self::$map = [];
	}
	public function getResult(){
		return $this->string;
	}
	private function explode(){
		preg_match_all('/{(.*?)}/ms', $this->string, $matches, PREG_SET_ORDER, 0);
		for ( $i = 0 ; $i < count($matches) ; $i++ ){
			$return['find'] = $matches[$i][0];
			$return['pattern'] = $matches[$i][1];
			if ( substr($matches[$i][1],0, strlen(self::startVariable)) == self::startVariable ){
				$return['otherValues'] = $this->findName($matches[$i][1]);
				$return['functionName'] = 'echo';
			} else {
				$onSpace = explode(' ',$matches[$i][1]);
				$return['functionName'] = trim($onSpace[0]);
				array_shift($onSpace);
				$generateExtra = ( empty($onSpace) ) ? null : implode(' ' , $onSpace )  ;
				$generateExtra = str_replace(self::parameterSeparator,' '.self::parameterSeparator,$generateExtra);
				$return['otherValues'] = $this->findName(  $generateExtra );
			}
			$return['functionName'] = str_replace(self::replaceWithEndFunctionName,'end' , strtolower($return['functionName']));
			if (method_exists($this, '_' . $return['functionName']) && is_callable(array($this, '_' . $return['functionName']))) {
				call_user_func([$this, '_' . $return['functionName']], $return);
			}
			unset($return);
		}
	}

	private function findName($string){
		if ( strlen($string) == 0 )
			return '';
		preg_match_all('/\[(.*)\]/', $string, $matchesInner, PREG_SET_ORDER, 0);
		for($i=0;$i <count($matchesInner) ; $i++){
			$string = str_replace($matchesInner[$i][0],'moldValueInner'.$i ,$string);
//			$this->moldShouldReplaceInTheEnd[  "'moldValueInner".$i."'" ] = $this->findName($matchesInner[$i][1]) ;
			$matchesInner[$i][1] = strings::deleteWordFirstString($matchesInner[$i][1],'[');
			$matchesInner[$i][1] = strings::deleteWordLastString($matchesInner[$i][1],']');
			$matchesInner[$i][1] = str_replace(['[',']'] , ['{','}'] , $matchesInner[$i][1]);
			$renderInner = new MoldRendering( '{'.$matchesInner[$i][1].'}' , $this->fileInfo ,$this->moldData , $this->version , true);
			$this->moldShouldReplaceInTheEnd[  "moldValueInner".$i ] = $renderInner->getResult() ;
		}

		$re = '/".*?"(*SKIP)(?!)|(?<!\w)\'.*?\'(?!\w)(*SKIP)(?!)|(\\'.self::variableArraySeparator.'\\'.self::startVariable.'|\\'.self::startVariable.'|\\'.self::variableArraySeparator.')[^->|\n| |,|\'|"|)]+/';
		preg_match_all($re, $string, $matches, PREG_SET_ORDER, 0);
		if ( !empty($matches)) {
			foreach ($matches as $patternKey => $pattern) {
				$allVariable = explode(self::startVariable, $pattern[0]);
				foreach ($allVariable as $oneVariablePatternIndex => $oneVariablePattern) {
					if (empty($oneVariablePattern)) {
						unset($allVariable[$oneVariablePatternIndex]);
						continue;
					}
					$allVariableArray = explode(self::variableArraySeparator, $oneVariablePattern);
					foreach ($allVariableArray as $oneVariableIndex => $oneVariable) {
						if (empty($oneVariable)) {
							unset($allVariableArray[$oneVariableIndex]);
							continue;
						}
						if (!is_numeric($oneVariable)) $allVariableArray[$oneVariableIndex] = "'" . $oneVariable . "'";
					}
					$allVariable[$oneVariablePatternIndex] = '$moldData->d()[' . implode('][', $allVariableArray) . ']';
				}
				$replaceWith = implode('[', $allVariable);
				for ($i = 1; $i < count($allVariable); $i++) $replaceWith .= ']';
				$string = str_replace($pattern[0], $replaceWith, $string);

				if ( strings::strhas($string , '|' ) ){
					$dataOfFunctions = explode('|', $string);
					$value = array_shift($dataOfFunctions);
					foreach ( $dataOfFunctions as $dataOfFunction ) {
						$funcValue = explode(':', $dataOfFunction);
						$funcName = array_shift($funcValue);
						array_unshift($funcValue, $value);
						if (method_exists($this, '___' . $funcName))
							$string = call_user_func_array([$this, '___' . $funcName], $funcValue);
						else
							$string = $value;
						$value = $string ;
					}
				}

			}
		}
		if ( !empty($this->moldShouldReplaceInTheEnd))
			foreach ($this->moldShouldReplaceInTheEnd as $moldShouldReplaceInTheEndIndex => $moldShouldReplaceInTheEnd) {
				$string = str_replace(["'".$moldShouldReplaceInTheEndIndex."'",$moldShouldReplaceInTheEndIndex],$moldShouldReplaceInTheEnd ,$string);
			}
		return $string ;

	}

	private function replace($find , $replace , $forceDo = false , $addPhp = true  ){
		if ( $this->runFromPhp ){
			if ($this->engineStatus or $forceDo) {
				$stringFirst = substr($this->string, 0, $this->lastEngineStatusFalse);
				$stringLast = substr($this->string, $this->lastEngineStatusFalse);
				$this->string = $stringFirst . preg_replace('/' . preg_quote($find, '/') . '/', $replace, $stringLast, 1);
			}
		} else {
			if ($this->engineStatus or $forceDo) {
				$stringFirst = substr($this->string, 0, $this->lastEngineStatusFalse);
				$stringLast = substr($this->string, $this->lastEngineStatusFalse);
				if ($addPhp) $replace = ($replace != '') ? '<?php ' . $replace . ' ?>' : '';
				$this->string = $stringFirst . preg_replace('/' . preg_quote($find, '/') . '/', $replace, $stringLast, 1);
			}
		}
	}

	private function _echo($data){
		if ( !$this->engineStatus )
			return ;
		if ( $this->runFromPhp ){
			$this->replace($data['find'] , $data['otherValues'] );
		} else {
			$this->replace($data['find'],  'echo ' . $data['otherValues'] . ' ; ');
		}
	}

	private function _if($data){
		if ( !$this->engineStatus )
			return ;
		if ( $this->runFromPhp ){
			$this->replace($data['find'] , ' ( ( '. $data['otherValues'] .' ) ? ' );
		} else
		$this->replace($data['find'] , 'if ( '.$data['otherValues'] .' ) { ');
	}
	private function _elseif($data){
		if ( !$this->engineStatus )
			return ;
		$this->replace($data['find'] , '} elseif ( '.$data['otherValues'] .' ) { ');
	}
	private function _else($data){
		if ( !$this->engineStatus )
			return ;
		if ( $this->runFromPhp ){
			$this->replace($data['find'] , ' : ' );
		} else
		$this->replace($data['find'] , '} else { ');
	}
	private function _endif($data){
		if ( !$this->engineStatus )
			return ;
		if ( $this->runFromPhp ){
			$this->replace($data['find'] ,  ' ) ');
		} else
		$this->replace($data['find'] , ' } ');
	}

	private function _set($data){
		if ( !$this->engineStatus )
			return ;
		if (preg_match('/(name)( *)=/', $data['otherValues']) and preg_match('/(value)( *)=/', $data['otherValues'])  ) {
			$re = '/name(\ *)=\s*([\S]+)/';
			preg_match_all($re, $data['otherValues'], $matches, PREG_SET_ORDER, 0);
			if (count($matches) != 1) die('error in set function1');
			$name = end($matches[0]);
			$re = '/value(\ *)=\s*([\S]+)/';
			preg_match_all($re, $data['otherValues'], $matches, PREG_SET_ORDER, 0);
			if (count($matches) != 1) die('error in set function1');
			$value = end($matches[0]);
			$this->replace($data['find'], '$moldData->set(' . trim($name) . ' , ' . trim($value) . ');');
		} else {
			$matches = explode(self::parameterSeparator , $data['otherValues'] );
			if (count($matches) != 2) die('error in set function');
			$this->replace($data['find'], '$moldData->set(' . trim($matches[0]) . ' , ' . trim($matches[1]) . ');');
		}
	}

	private function _math($data){
		if ( !$this->engineStatus )
			return ;
		if (preg_match('/(format)( *)=/', $data['otherValues']) ) {

			$re = '/(format)\s*=\s*(["\'])(?:(?=(\\\\?))\3.)*?\2/';
			preg_match_all($re, $data['otherValues'], $matches, PREG_SET_ORDER, 0);
			if ( count($matches) != 1 )
				die('error in math function!(format is required and unique)');
			$format = explode($matches[0][2] , $matches[0][0])[1];
			$data['otherValues'] = str_replace($matches[0][0] ,'', $data['otherValues']);

			$re = '/([\S]+)\s*=\s*([\S]+)/';
			preg_match_all($re, $data['otherValues'], $matches, PREG_SET_ORDER, 0);
			for( $i = 0 ; $i < count($matches ) ; $i++ ){
				$dataOfFormat[ trim(strtolower(str_replace(["\"","'"],"" , $matches[$i][1]))) ] = $matches[$i][2];
			}
			if ( isset($dataOfFormat['set']) ){
				$set = $dataOfFormat['set'];
				unset($dataOfFormat['set']);
			}
			if ( ! empty($dataOfFormat) ){
				foreach ( $dataOfFormat as $variable => $value ){
					$format = preg_replace('(\b('.$variable.')\b)' , $value , $format);
				}
			}
			if ( isset($set) ){
				$this->replace($data['find'], '$moldData->set(' . trim($set) . ' , (' . trim($format) . ') );');
			} else {
				if ( $this->runFromPhp ) {
					$this->replace($data['find'], ' (' . trim($format) . ') ');
				}else
					$this->replace($data['find'], 'echo (' . trim($format) . ') ; ');
			}
		} else {
			die('error in math function!(format is required)');
		}
	}

	private function _for($data){
		if ( !$this->engineStatus )
			return ;
		if (preg_match('/(name)( *)=/', $data['otherValues']) and preg_match('/(start)( *)=/', $data['otherValues']) and preg_match('/(end)( *)=/', $data['otherValues'])  ) {
			$re = '/name(\ *)=\s*([\S]+)/';
			preg_match_all($re, $data['otherValues'], $matches, PREG_SET_ORDER, 0);
			if (count($matches) != 1) die('error in for function');
			$name = end($matches[0]);
			$re = '/start(\ *)=\s*([\S]+)/';
			preg_match_all($re, $data['otherValues'], $matches, PREG_SET_ORDER, 0);
			if (count($matches) != 1) die('error in for function');
			$start = end($matches[0]);
			$re = '/end(\ *)=\s*([\S]+)/';
			preg_match_all($re, $data['otherValues'], $matches, PREG_SET_ORDER, 0);
			if (count($matches) != 1) die('error in for function');
			$end = end($matches[0]);
			$re = '/counter(\ *)=\s*([\S]+)/';
			preg_match_all($re, $data['otherValues'], $matches, PREG_SET_ORDER, 0);
			if (count($matches) != 1)
				$counter = +1;
			else
				$counter = end($matches[0]);
			$this->replace($data['find'], 'for ( $'.trim($name).' = '.$start.' ; $'.trim($name).' < '.$end.' ; $'.trim($name).' = $'.trim($name).'+('.$counter.') ) { $moldData->set("' . trim($name) . '" , $'.trim($name).' );');
		} else {
			$extraData = explode(self::parameterSeparator,$data['otherValues']);
			$extraDataSet = [];
			foreach ($extraData as $index => $value ){
				if ( $value == "")
					continue;
				$extraDataSet[] = trim($value) ;
			}
			if ( ! isset($extraDataSet[3]) ) $extraDataSet[3] = 1 ;
			if ( ! isset($extraDataSet[0]) or  ! isset($extraDataSet[1])  or  ! isset($extraDataSet[2]) )
				die('error in for function');
			$this->replace($data['find'], 'for ( $'.trim($extraDataSet[0]).' = '.$extraDataSet[1].' ; $'.trim($extraDataSet[0]).' < '.$extraDataSet[2].' ; $'.trim($extraDataSet[0]).' = $'.trim($extraDataSet[0]).'+('.$extraDataSet[3].') ) { $moldData->set("' . trim($extraDataSet[0]) . '" , $'.trim($extraDataSet[0]).' );');
		}
	}
	private function _endfor($data){
		if ( !$this->engineStatus )
			return ;
		$this->replace($data['find'], ' } ');
	}
	private function _foreach($data){
		if ( !$this->engineStatus )
			return ;
		if (preg_match('/(from)( *)=/', $data['otherValues']) and preg_match('/(key)( *)=/', $data['otherValues']) and preg_match('/(value)( *)=/', $data['otherValues'])  ) {
			$re = '/from(\ *)=\s*([\S]+)/';
			preg_match_all($re, $data['otherValues'], $matches, PREG_SET_ORDER, 0);
			if (count($matches) != 1) die('error in foreach function');
			$from = end($matches[0]);
			$re = '/key(\ *)=\s*([\S]+)/';
			preg_match_all($re, $data['otherValues'], $matches, PREG_SET_ORDER, 0);
			if (count($matches) != 1) die('error in foreach function');
			$key = end($matches[0]);
			$re = '/value(\ *)=\s*([\S]+)/';
			preg_match_all($re, $data['otherValues'], $matches, PREG_SET_ORDER, 0);
			if (count($matches) != 1) die('error in foreach function');
			$value = end($matches[0]);
			$this->moldForeachElse[] = $from ;
			$this->replace($data['find'], 'foreach ('.$from.' as $'.trim($key).' => $'.trim($value).' ) { $moldData->set("' . trim($key) . '" , $'.trim($key).' ); $moldData->set("' . trim($value) . '" , $'.trim($value).' );');
		} else {
			$extraData = explode(self::parameterSeparator,$data['otherValues']);
			$extraDataSet = [];
			foreach ($extraData as $index => $value ){
				if ( $value == "")
					continue;
				$extraDataSet[] = trim($value) ;
			}
			if ( ! isset($extraDataSet[0]) or  ! isset($extraDataSet[1])  or  ! isset($extraDataSet[2]) )
				die('error in foreach function');
			$this->moldForeachElse[] = $extraDataSet[0] ;
			$this->replace($data['find'], 'foreach ('.$extraDataSet[0].' as $'.trim($extraDataSet[1]).' => $'.trim($extraDataSet[2]).' ) { $moldData->set("' . trim($extraDataSet[1]) . '" , $'.trim($extraDataSet[1]).' ); $moldData->set("' . trim($extraDataSet[2]) . '" , $'.trim($extraDataSet[2]).' );');
		}
	}
	private function _endforeach($data){
		if ( !$this->engineStatus )
			return ;
		$endedForeach = array_pop($this->moldForeachElse);
		$this->replace($data['find'], ' } ');
	}

	private function _continue($data){
		if ( !$this->engineStatus )
			return ;
		$this->replace($data['find'] , ' continue ;');
	}
	private function _foreachelse($data){
		if ( !$this->engineStatus )
			return ;
		$endedForeach = end($this->moldForeachElse);
		if ( $endedForeach != null )
			$this->replace($data['find'], ' } if ( ! is_array('.$endedForeach.') ) { ');
		else
			$this->replace($data['find'], ' } if ( 1 == 0 ) { ');
	}

	private function _stop($data){
		$this->engineStatus = false ;
		$this->replace($data['find'],'' , true);
	}
	private function _start($data){
		$this->lastEngineStatusFalse = strpos($this->string ,"{start}",$this->lastEngineStatusFalse );
		$this->engineStatus = true ;
		$this->replace($data['find'],'' );
	}
	private function _lang($data){
		if ( !$this->engineStatus )
			return ;
		if ( $this->runFromPhp ){
			$this->replace($data['find'],'rlang('.$data['otherValues'].')' );
		} else {
			$this->replace($data['find'],'lang('.$data['otherValues'].') ; ' );
		}
	}

	private function _l($data){
		if ( !$this->engineStatus )
			return ;
		if ( $this->runFromPhp ){
			$this->replace($data['find'],'rlang('.$data['otherValues'].')' );
		} else {
			$this->replace($data['find'],'lang('.$data['otherValues'].') ; ' );
		}
	}

	private function __($data){
		if ( !$this->engineStatus )
			return ;
		if ( $this->runFromPhp ){
			$this->replace($data['find'],'rlang('.$data['otherValues'].')' );
		} else {
			$this->replace($data['find'],'lang('.$data['otherValues'].') ; ' );
		}
	}

	private function _php($data){
		if ( !$this->engineStatus )
			return ;
		$this->replace($data['find'],'<?php ' , false,false );
	}

	private function _endphp($data){
		if ( !$this->engineStatus )
			return ;
		$this->replace($data['find'],' ?>' , false,false );
	}

	private function _currentApp($data){
		if ( !$this->engineStatus )
			return ;
		if ( $this->runFromPhp ){
			$this->replace($data['find'] ,  \app::getApp());
		} else
		$this->replace($data['find'], 'echo "'. \app::getApp().'"', false );
	}

	private function _assets($data){
		if ( !$this->engineStatus )
			return ;
		$value = explode(self::parameterSeparator ,$data['otherValues']);
		$pathAndFileName =  str_replace(['/', '\\', '>',DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR], DIRECTORY_SEPARATOR, trim($value[0]));
		$pathAndFileName = str_replace(['"','\''] ,'' ,$pathAndFileName);
		$moldFile = new MoldFiles($this->moldData);
		$moldFile->checkFileISMoldOrNOt($pathAndFileName,$this->fileInfo['app']);
		$listDirToFile = explode(DIRECTORY_SEPARATOR,$pathAndFileName);
		$fileName = array_pop($listDirToFile);
		$extFile = strtolower(file::ext_file($fileName));
		$filePath = implode(DIRECTORY_SEPARATOR,$listDirToFile);
		$cacheFilePath = \app::getAppPath('cache/theme/assets');
		$fileContents = file_get_contents($pathAndFileName);
		$funcName = 'assetsFileCache_'.strings::generateRandomLowString();
		$cacheFileName = strings::get_unique_string( strings::generateRandomLowString(5) ).'.'.$fileName;
		$php = "if ( ! function_exists('".$funcName ."') ) { function ".$funcName .' ( &$moldData ) { ?>' ;
		$phpModel = new MoldRendering($fileContents , $this->fileInfo ,$this->moldData,$this->version) ;
		$php .= $phpModel->getResult();
		$php .= "<?php }  } ";
		eval($php);
		ob_start();
		call_user_func_array($funcName , [&$this->moldData] );
		$render = ob_get_clean();
		$php = "<?php\n";
		$php .= "\t/* mold version " . $this->version . " , created at : " . date('Y-m-d H:i:s') . "\n";
		$php .= "\t\t from \"" . $pathAndFileName . "\" */ \n";
		$php .= "\n\n";
		$php .= "if ( " ;
		$php .= ' $this->decodeCacheProperties( array ( ' . "\n";
		$php .= "\t\t 'version' => '" . $this->version . "' ,\n";
		$php .= "\t\t 'file' => '" . $fileName . "' ,\n";
		$php .= "\t\t 'path' => '" . $pathAndFileName . "' ,\n";
		$php .= "\t\t 'app' => '" . \app::getApp() . "' ,\n";
		$php .= "\t\t 'parentSize' => '" . file::file_size($pathAndFileName) . "' ,\n";
		$php .= "\t\t 'parentCreatedAt' => '" . file::file_time($pathAndFileName) . "' ,\n";
		$php .= "\t\t 'createdAt' => '" . time() . "', \n";
		$php .= "\t\t 'lifeTime' => '900' \n";
		$php .= "\t ) , true ) ) { ?>\n";
		$php .= $render ;
		$php .= "\n<?php } else die('Mold can not find assets file or the file is terminated!'); ";
		file::generate_file($cacheFilePath.DIRECTORY_SEPARATOR.$cacheFileName.'.php' , $php );
		$link = \app::getFullRequestUrl() ;
		$link .= ( strings::strhas($link,'?') ) ? '&' : '?' ;
		$link .= 'moldAssetsFileLoader='.$cacheFileName ;
		$echo = 'echo \''.( ( $extFile == 'js') ? '<script src="' . $link . '" ></script>' : ( $extFile == 'css' ) ? '<link rel="stylesheet" type="text/css" href="'.$link.'" />' : $link ).'\' ;' ;

		$this->replace($data['find'],$echo);
	}


	private function _cache($data){
		if ( !$this->engineStatus )
			return ;
		$value = explode(self::parameterSeparator ,$data['otherValues']);
		$moldFile = new MoldFiles($this->moldData);
		$time = trim($value[2]);
		$savePatchFileWithName =  str_replace(['/', '\\', '>',DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR], DIRECTORY_SEPARATOR, trim($value[1]));
		$savePatchFileWithName = str_replace(['"','\'','..'.DIRECTORY_SEPARATOR,'.'.DIRECTORY_SEPARATOR ] ,'' ,$savePatchFileWithName);
		$listDirToFile = explode(DIRECTORY_SEPARATOR,$savePatchFileWithName);
		$fileName = array_pop($listDirToFile);
		$extFile = strtolower(file::ext_file($fileName));
		$link = \app::getAppLink('theme/'.$this->fileInfo['folder'].$savePatchFileWithName ,$this->fileInfo['app']);
		$echo = 'echo \''.( ( $extFile == 'js') ? '<script src="' . $link . '" ></script>' : ( $extFile == 'css' ) ? '<link rel="stylesheet" type="text/css" href="'.$link.'" />' : $link ).'\' ;' ;
		if ( is_file($savePatchFileWithName) ){
			if ( file::file_time($savePatchFileWithName) + $time > time() ) {
				$this->replace($data['find'], $echo);
				return ;
			} else
				unlink($savePatchFileWithName);
		}
		$pathAndFileName =  str_replace(['/', '\\', '>',DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR], DIRECTORY_SEPARATOR, trim($value[0]));
		$pathAndFileName = str_replace(['"','\''] ,'' ,$pathAndFileName);
		$moldFile->checkFileISMoldOrNOt($pathAndFileName,$this->fileInfo['app']);
		$fileContents = file_get_contents($pathAndFileName);
		$funcName = 'assetsFileCache_'.strings::generateRandomLowString();
		$php = "if ( ! function_exists('".$funcName ."') ) { function ".$funcName .' ( &$moldData ) { ?>' ;
		$phpModel = new MoldRendering($fileContents , $this->fileInfo ,$this->moldData,$this->version) ;
		$php .= $phpModel->getResult();
		$php .= "<?php }  } ";
		eval($php);
		ob_start();
		call_user_func_array($funcName , [&$this->moldData] );
		$render = ob_get_clean();
		$render = "\t// mold version " . $this->version . " , created at : " . date('Y-m-d H:i:s') . "\n\n" . $render;

		$listDirToFile = explode(DIRECTORY_SEPARATOR, $this->fileInfo['path']);
		$fileName = array_pop($listDirToFile);
		$linkToTemplate = implode(DIRECTORY_SEPARATOR, $listDirToFile);
		$linkToTemplate =  strings::strLastHas($link,DIRECTORY_SEPARATOR) ? $linkToTemplate : $linkToTemplate.DIRECTORY_SEPARATOR;

		file::generate_file($linkToTemplate.$savePatchFileWithName , $render );
		$this->replace($data['find'],$echo);
	}

	private function _view($data){
		if ( !$this->engineStatus )
			return ;
		$savePatchFileWithName =  str_replace(['/', '\\', '>',DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR], DIRECTORY_SEPARATOR, trim($data['otherValues']));
		$savePatchFileWithName = str_replace(['"','\''] ,'' ,$savePatchFileWithName);
		$temp = explode(DIRECTORY_SEPARATOR,$savePatchFileWithName);
		$fileName = array_pop($temp);
		$moldFiles = new MoldFiles($this->moldData);
		$this->replace($data['find'], 'call_user_func_array(\''.$moldFiles->generateFuncName($savePatchFileWithName).'\' , [&$moldData] );' , false);
		$this->string .= ' <?php } $this->innerView([\'app\' => \''.$this->fileInfo['app'].'\' ,\'folder\' => \''.$this->fileInfo['folder'].'\' ,\'path\' => \''.$savePatchFileWithName.'\' ,\'file\' => \''.$fileName.'\',\'innerView\' => true ] ) ; { ?>' ;
	}

	private function _map($data){
		if ( !$this->engineStatus )
			return ;
		$re = '/{\s*map[^}]*}(.*?){\s*(\/|end)\s*map}/ms';
		preg_match_all($re, $this->string, $matches, PREG_SET_ORDER, 0);
		for ( $i = 0 ; $i < count($matches) ; $i++ ){
			$this->replace($matches[$i][0],"", false,false);
			$name = strings::deleteWordFirstString($matches[$i][0],'{map ');
			$names = explode('}',$name);
			$name = str_replace(['"','\''] ,'' ,trim($names[0]));
			$name = str_replace(['/','*','~','-','.','\\'],'_', $name);
			if ( isset( self::$map[$name] ))
				self::$map[$name]++;
			else
				self::$map[$name] = 0 ;
			$moldRendering = new MoldRendering($matches[$i][1],$this->fileInfo,$this->moldData,$this->version );
			$this->string .= ' <?php }  function moldBlock_'.$name.'_'.self::$map[$name].'(&$moldData){ ?>'.$moldRendering->getResult() ;
		}

	}

	private function _url($data){
		if ( !$this->engineStatus )
			return ;
		switch (trim(strtolower($data['otherValues']))) {
			case 'theme':
				$link = \app::getAppLink('theme/' . $this->fileInfo['folder'], $this->fileInfo['app']);
				$link =  strings::strLastHas($link,'/') ? $link : $link.'/';
				break;
			case 'patch':
				$listDirToFile = explode(DIRECTORY_SEPARATOR, $this->fileInfo['path']);
				$fileName = array_pop($listDirToFile);
				$link = implode(DIRECTORY_SEPARATOR, $listDirToFile);
				$link =  strings::strLastHas($link,DIRECTORY_SEPARATOR) ? $link : $link.DIRECTORY_SEPARATOR;
				break;
			case 'current':
				$link = \app::getFullRequestUrl();
				$link =  strings::strLastHas($link,'/') ? $link : $link.'/';
				$this->replace($data['find'],' echo \app::getFullRequestUrl(); ' , false);
				return ;
				break;
			case '':
				$link = \app::getBaseAppLink();
				$link =  strings::strLastHas($link,'/') ? $link : $link.'/';
				break;
			default :
				$link = \app::getBaseAppLink(null, $data['otherValues']);
				$link =  strings::strLastHas($link,'/') ? $link : $link.'/';
				break;
		}
		if ( $this->runFromPhp ){
			$this->replace($data['find'] , $link);
		} else
		$this->replace($data['find'],$link , false,false);
	}

	private function _endmap($data) {
		if ( !$this->engineStatus )
			return ;
		$this->replace($data['find'],' ' , false,false);
	}

	private function _call ( $data ){
		if ( !$this->engineStatus )
			return ;
		$data['otherValues'] = trim($data['otherValues']);
		$this->replace($data['find'],' $i = 0 ; while ( function_exists(\'moldBlock_'.$data['otherValues'].'_\'.$i )) { call_user_func_array(\'moldBlock_'.$data['otherValues'].'_\'.$i ,[&$moldData] ); $i++ ; } ');
	}


	private function ___count($value){
		if ( !$this->engineStatus )
			return ;
		return '((is_array('.$value.') ) ? count('.$value.') : 0 )' ;
	}

	private function ___nl2br($value){
		if ( !$this->engineStatus )
			return ;
		return ' nl2br('.$value.') ' ;
	}

	private function ___truncate($value,$number = 40 , $more = '"..."'){
		if ( !$this->engineStatus )
			return ;
		return '((strlen('.$value.') > '.$number.' ) ? substr('.$value.' , 0 , '.$number.').'.$more.' : '.$value.' )' ;
	}

	private function ___number_format($value,$decimals = 0 ,$dec_point ='.',$thousands_sep = ','){
		if ( !$this->engineStatus )
			return ;
		return 'number_format('.$value.','.$decimals.',"'.$dec_point.'","'.$thousands_sep.'")' ;
	}

	private function ___str_replace($value, $search , $replace ){
		if ( !$this->engineStatus )
			return ;
		return 'str_replace('.$search.' ,'.$replace.','.$value.' )' ;
	}

	private function ___date_format($value,$format){
		if ( !$this->engineStatus )
			return ;
		return '( ( ctype_digit('.$value.') && strtotime(date(\'Y-m-d H:i:s\','.$value.')) === (int)'.$value.' ) ? date('.$format.','.$value.') : date('.$format.',strtotime(str_replace(\'/\', \'-\','.$value.'))) ) ' ;
	}

	private function ___jDate($value,$format,$time_Zone = '"Asia/Tehran"' , $tr_num = '"en"'){
		if ( !$this->engineStatus )
			return ;
		return '( ( ctype_digit('.$value.') && strtotime(date(\'Y-m-d H:i:s\','.$value.')) === (int)'.$value.' ) ? \paymentCms\component\JDate::jdate('.$format.','.$value.',"",'.$time_Zone.','.$tr_num.') : \paymentCms\component\JDate::jdate('.$format.',strtotime(str_replace(\'/\', \'-\','.$value.')),"",'.$time_Zone.','.$tr_num.') ) ' ;
	}
}