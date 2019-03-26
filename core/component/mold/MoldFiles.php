<?php

namespace paymentCms\component\mold;

use paymentCms\component\file;
use paymentCms\component\httpHeader;
use paymentCms\component\strings;

/**
 * Created by Yeganehha .
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 3/23/2019
 * Time: 12:48 PM
 * project : paymentCms
 * virsion : 0.0.0.1
 * update Time : 3/23/2019 - 12:48 PM
 * Discription of this Page :
 */

class MoldFiles {

	private $moldData = null ;
	private $path = null ;
	private $app = null ;
	private $folder = null ;
	private $lastHeaderFile = null ;
	private $lastFooterFile = null ;
	private $lastBodyFile = null ;
	private $files = [] ;
	private $debugFilePath = [] ;
	private $minifyHtml = false ;
	private $cacheLifeTime = false ;
	const version = '0.0.0.1' ;

	public function __construct(&$moldData) {
		$this->moldData = $moldData;
		$this->setPath('default');
		MoldRendering::emptyMap();
	}

	public function setPath($folder,$app = null ){
		$app = ( is_null($app) ) ?  \app::getApp() : $app ;
		$folder = ( is_null($folder) ) ?  '' : $folder.'/' ;
		$this->path = $this->directorySeparator( \app::getAppPath('theme/'.$folder,$app) );
		$this->app = $app ;
		$this->folder = $folder ;
	}

	public function setDirectPath($path){
		$this->path = $this->directorySeparator( $path );
	}

	public function appendView(){
		$files = func_get_args();
		$section = $files[0];
		array_shift($files);
		if ( $section == 'header' ){
			array_unshift($files , $this->lastHeaderFile);
			call_user_func_array(array( $this,'addViewAfter' ) , $files );
			$endReference = explode(DIRECTORY_SEPARATOR, $this->directorySeparator( end($files) ) ) ;
			$this->lastHeaderFile = end($endReference ) ;
			if ( $this->lastBodyFile == null )
				$this->lastBodyFile = $this->lastHeaderFile ;
		} elseif ( $section == 'footer' ){
			array_unshift($files , null);
			call_user_func_array(array( $this,'addViewBefore' ) , $files  );
			$endReference = explode(DIRECTORY_SEPARATOR, $this->directorySeparator( end($files) ) ) ;
			$this->lastFooterFile = end($endReference ) ;
		} else {
			array_unshift($files , $this->lastBodyFile);
			call_user_func_array(array( $this,'addViewAfter' ) , $files );
			$endReference = explode(DIRECTORY_SEPARATOR, $this->directorySeparator( end($files) ) ) ;
			$this->lastBodyFile = end($endReference ) ;
			if ( $this->lastFooterFile == null )
				$this->lastFooterFile = $this->lastBodyFile ;
		}

	}

	public function addViewAfter(){
		if (func_num_args() > 1) {
			$files = func_get_args();
			$oldFile = $files[0];
			array_shift($files);
			if ( $oldFile == null ) {
				foreach ( array_reverse($files) as $newFile) {
					$newFileWithPath = $this->directorySeparator($newFile);
					$newFileWithPathArray = explode(DIRECTORY_SEPARATOR, $newFileWithPath);
					$nameFileToInsert =  end($newFileWithPathArray) ;
					array_pop($newFileWithPathArray);
					$newPathFileToInsert = $this->path  . implode(DIRECTORY_SEPARATOR,$newFileWithPathArray). ( ( count($newFileWithPathArray) > 0 ) ? DIRECTORY_SEPARATOR : '' ) ;
					array_unshift($this->files , ['file' => $nameFileToInsert , 'directory' => $newPathFileToInsert , 'path' => $newPathFileToInsert . $nameFileToInsert , 'app' => $this->app , 'folder' => $this->folder ] );
				}
			} else {
				$oldFileWithPath = $this->directorySeparator($oldFile);
				$oldFileWithPathArray = explode(DIRECTORY_SEPARATOR, $oldFileWithPath);
				$tempIndex = array_search( end($oldFileWithPathArray) , array_column($this->files , 'file' ) );
				if ( $tempIndex === false and end($oldFileWithPathArray) != null )
					return ;
				$tempFirstFiles = array_slice($this->files, 0, $tempIndex+1);
				$tempLastFiles = array_slice($this->files,  $tempIndex+1 ) ;
				foreach ( $files as $newFile) {
					$newFileWithPath = $this->directorySeparator($newFile);
					$newFileWithPathArray = explode(DIRECTORY_SEPARATOR, $newFileWithPath);
					$nameFileToInsert =  end($newFileWithPathArray) ;
					array_pop($newFileWithPathArray);
					$path = $this->path . implode(DIRECTORY_SEPARATOR,$newFileWithPathArray) . ( ( count($newFileWithPathArray) > 0 ) ? DIRECTORY_SEPARATOR : '' ) ;
					array_push($tempFirstFiles,  ['file' => $nameFileToInsert , 'directory' => $path , 'path' => $path  . $nameFileToInsert , 'app' => $this->app , 'folder' => $this->folder ] );
				}
				$this->files = array_merge($tempFirstFiles,$tempLastFiles);
			}
		}
	}

	public function addViewBefore(){
		if (func_num_args() > 1) {
			$files = func_get_args();
			$oldFile = $files[0];
			array_shift($files);
			if ( $oldFile == null ) {
				foreach ($files as $newFile) {
					$newFileWithPath = $this->directorySeparator($newFile);
					$newFileWithPathArray = explode(DIRECTORY_SEPARATOR, $newFileWithPath);
					$nameFileToInsert =  end($newFileWithPathArray) ;
					array_pop($newFileWithPathArray);
					$path = $this->path  . implode(DIRECTORY_SEPARATOR,$newFileWithPathArray) . ( ( count($newFileWithPathArray) > 0 ) ? DIRECTORY_SEPARATOR : '' );
					$this->files[] =  ['file' => $nameFileToInsert , 'directory' => $path , 'path' => $path  . $nameFileToInsert , 'app' => $this->app , 'folder' => $this->folder ] ;
				}
			} else {
				$oldFileWithPath = $this->directorySeparator($oldFile);
				$oldFileWithPathArray = explode(DIRECTORY_SEPARATOR, $oldFileWithPath);
				$tempIndex = array_search( end($oldFileWithPathArray) , array_column($this->files , 'file' ) );
				if ( $tempIndex === false and end($oldFileWithPathArray) != null )
					return ;
				$tempFirstFiles = array_slice($this->files, 0, $tempIndex);
				$tempLastFiles = array_slice($this->files,  $tempIndex ) ;
				foreach ($files as $newFile) {
					$newFileWithPath = $this->directorySeparator($newFile);
					$newFileWithPathArray = explode(DIRECTORY_SEPARATOR, $newFileWithPath);
					$nameFileToInsert =  end($newFileWithPathArray) ;
					array_pop($newFileWithPathArray);
					$path = $this->path  . implode(DIRECTORY_SEPARATOR,$newFileWithPathArray) . ( ( count($newFileWithPathArray) > 0 ) ? DIRECTORY_SEPARATOR : '' );
					array_push($tempFirstFiles, ['file' => $nameFileToInsert , 'directory' => $path , 'path' => $path  . $nameFileToInsert , 'app' => $this->app , 'folder' => $this->folder ] );
				}
				$this->files = array_merge($tempFirstFiles,$tempLastFiles);
			}
		}
	}

	public function unshow(){
		$ParametersCallWithMethod = func_get_args();
		if (is_array( $ParametersCallWithMethod )  && count($ParametersCallWithMethod) > 1 ) {
			foreach ($ParametersCallWithMethod as $keyIndex => $key) {
				$FileWithPath = $this->directorySeparator($key);
				$FileWithPathArray = explode(DIRECTORY_SEPARATOR, $FileWithPath);
				$tempIndex = array_search( end($FileWithPathArray) , array_column($this->files , 'file' ) );
				if ( $tempIndex === false and end($FileWithPathArray) != null )
					continue ;
				array_splice($this->files, $tempIndex, 1);
			}
		} elseif ( is_array( $ParametersCallWithMethod ) && count($ParametersCallWithMethod) == 1  )  {
			$FileWithPath = $this->directorySeparator($ParametersCallWithMethod[0]);
			$FileWithPathArray = explode(DIRECTORY_SEPARATOR, $FileWithPath);
			$tempIndex = array_search( end($FileWithPathArray) , array_column($this->files , 'file' ) );
			if ( $tempIndex === false and end($FileWithPathArray) != null )
				return ;
			array_splice($this->files, $tempIndex, 1);
		}
	}
	public function listView(){
		return array_column($this->files , 'path');
	}

	public function cache($cacheFilePath,$php,$runAfterCreatFile){
		$cacheFilePath = $this->directorySeparator($cacheFilePath) ;
		if ( is_file($cacheFilePath) )
			unlink($cacheFilePath);
		File::generate_file($cacheFilePath , $php );
		if ( $runAfterCreatFile )
			include $cacheFilePath ;
	}

	private function compileFile($fileInfo ,$cacheFilePath , $runAfterCreatFile = false  ){
		$cacheIt = ( $this->cacheLifeTime !== false ) ? true : false;
		$fileContents = file_get_contents($fileInfo['path']);
		if ( $this->minifyHtml )
			$fileContents = $this->Minify_Html($fileContents);
		$php = '';
		if ( $cacheIt ) {
			$php .= "<?php\n";
			$php .= "\t/* mold version " . self::version . " , created at : " . date('Y-m-d H:i:s') . "\n";
			$php .= "\t\t from \"" . $fileInfo['path'] . "\" */ \n";
			$php .= "\n\n";
		}
		$php .= "if ( " ;
		if ( $cacheIt ) {
			$php .= ' $this->decodeCacheProperties( array ( ' . "\n";
			$php .= "\t\t 'version' => '" . self::version . "' ,\n";
			$php .= "\t\t 'file' => '" . $fileInfo['file'] . "' ,\n";
			$php .= "\t\t 'path' => '" . $fileInfo['path'] . "' ,\n";
			$php .= "\t\t 'app' => '" . $fileInfo['app'] . "' ,\n";
			$php .= "\t\t 'folder' => '" . $fileInfo['folder'] . "' ,\n";
			$php .= "\t\t 'parentSize' => '" . File::file_size($fileInfo['path']) . "' ,\n";
			$php .= "\t\t 'parentCreatedAt' => '" . File::file_time($fileInfo['path']) . "' ,\n";
			$php .= "\t\t 'createdAt' => '" . time() . "', \n";
			$php .= "\t\t 'lifeTime' => '" . ( intval($this->cacheLifeTime) ) . "' \n";
			$php .= "\t ) ) and  ";
		}
		$php .= "\t ! function_exists('".$this->generateFuncName($fileInfo['path']) ."') ) {\n function ".$this->generateFuncName($fileInfo['path']) .' ( &$moldData ) { '."\n\t".'/* @var \mold\moldData $moldData */'."\n?>\n" ;

		$phpModel = new MoldRendering($fileContents , $fileInfo ,$this->moldData,self::version) ;
		$php .= $phpModel->getResult();
		$php .= "\n<?php } \n } ";

		if ( $cacheIt )
			$this->cache($cacheFilePath,$php,$runAfterCreatFile);
		return $php ;
	}

	public function render(){
		$functionCall = [] ;
		$evalString = '';
		register_shutdown_function(array($this, 'cacheFileErrorHandler'));
		ini_set('display_errors', false);
		for( $i = 0 ; $i < count($this->files) ; $i++ ){
			$return = $this->loadFile($this->files[$i],$i);
			if ( $return['eval'] != null )
				$evalString .= $return['eval'];
			if ( ! ( isset($this->files[$i]['innerView']) and  $this->files[$i]['innerView'] ) )
				$functionCall[] = $return['function'] ;
		}
		if ( $this->cacheLifeTime === false )
			eval($evalString);
		ob_start();
		for( $i = 0 ; $i < count($functionCall) ; $i++ ){
			call_user_func_array($functionCall[$i] , [&$this->moldData] );
		}
		$render = ob_get_clean();
		return $render ;
	}


	private function  loadFile($currentTemplate,$i){
		$this->checkFileISMoldOrNOt($currentTemplate['path'],$currentTemplate['app']);
		$return = ['eval' => null , 'function' => null ];
		if ( ! is_file($currentTemplate['path']) ) {
			// TODO: send exception and delete all cache of this file
			die('File not exist ! file path : ' . $currentTemplate['path']);
		}
		$cacheFilePath = $this->generateCacheFilePath($currentTemplate) ;
		$this->debugFilePath[md5($cacheFilePath)] = $i ;
		if ( $this->cacheLifeTime !== false ) {
			if (!is_file($cacheFilePath)) {
				$this->compileFile($currentTemplate, $cacheFilePath);
			}
			if (function_exists($this->generateFuncName($currentTemplate['path']))) {
				$return['function'] = $this->generateFuncName($currentTemplate['path']);
			} else {
				require_once $cacheFilePath;
				$return['function'] = $this->generateFuncName($currentTemplate['path']);
			}
		} else {
			$return['eval'] =  $this->compileFile($currentTemplate, $cacheFilePath );
			$return['function'] = $this->generateFuncName($currentTemplate['path']) ;
		}
		return $return ;
	}

	public function innerView($currentTemplate){
		$this->files[] = $currentTemplate ;
		$i = count($this->files)-1;
		$return = $this->loadFile($currentTemplate,$i);
		if ( $return['eval'] != null )
			eval($return['eval']);
	}

	public function cacheFileErrorHandler(){
		$lasterror = error_get_last();
		if ( is_null($lasterror)) return false;
		ob_clean();
		switch($lasterror['type'])
		{
			case E_ERROR:
				$type =  'E_ERROR';break;
			case E_WARNING:
				$type =  'E_WARNING';break;
			case E_PARSE:
				$type =  'E_PARSE';break;
			case E_NOTICE:
				$type =  'E_NOTICE';break;
			case E_CORE_ERROR:
				$type =  'E_CORE_ERROR';break;
			case E_CORE_WARNING:
				$type =  'E_CORE_WARNING';break;
			case E_COMPILE_ERROR:
				$type =  'E_COMPILE_ERROR';break;
			case E_COMPILE_WARNING:
				$type =  'E_COMPILE_WARNING';break;
			case E_USER_ERROR:
				$type =  'E_USER_ERROR';break;
			case E_USER_WARNING:
				$type =  'E_USER_WARNING';break;
			case E_USER_NOTICE:
				$type =  'E_USER_NOTICE';break;
			case E_STRICT:
				$type =  'E_STRICT';break;
			case E_RECOVERABLE_ERROR:
				$type =  'E_RECOVERABLE_ERROR';break;
			case E_DEPRECATED:
				$type =  'E_DEPRECATED';break;
			case E_USER_DEPRECATED:
				$type =  'E_USER_DEPRECATED';break;
		}

		$file = $this->files[($this->debugFilePath[md5( $lasterror['file'] )])] ;
		$directors  = explode(DIRECTORY_SEPARATOR , $lasterror['file'] );
		array_pop($directors);
		$themeFolder = array_pop($directors);
		$cacheFolder = array_pop($directors);
		httpHeader::generateStatusCodeHTTP(500);
		echo '<div style="margin: 20px;"><strong>MOLD ERROR : </strong><br><hr>'."\n";
		echo 'Error : <strong>'.$type.' ! </strong>'. str_replace(["\n","\r"] , "" ,$lasterror['message'] ).'<br><br>'."\n";
		echo 'Mold file name : '.$file['file'].'<br><br>'."\n";
		echo 'Error line : '.($lasterror['line']-19).'<br><br>'."\n";
		echo 'App : '.$file['app'].'<br><br>'."\n";
		echo 'Theme Folder : '.strings::deleteWordLastString($file['folder'],'/').'<br><br>'."\n";
		echo 'Path : '.substr($file['path'], strpos($file['path'],$file['app'])).'<br></div>'."\n";
		if ( $themeFolder == 'theme' and $cacheFolder == 'cache')unlink($lasterror['file']);
		exit;
	}
	public function renderAssets($file){
		$cacheFilePath = \app::getAppPath('cache/theme/assets') . DIRECTORY_SEPARATOR.$file.'.php';
		if (!is_file($cacheFilePath)) {
			$this->deleteAssetsFile();
			die('Mold can not find assets file or the file is terminated!');
		}
		$extFile = strtolower(File::ext_file($file));
		if ( $extFile == 'js' ){
			header('Content-Type: text/javascript');
		} elseif ( $extFile == 'css' ){
			header('Content-Type: text/css');
		} elseif ( $extFile == 'json' ){
			header('Content-Type: application/json');
		} elseif ( $extFile == 'rss' ){
			header('Content-Type: application/rss+xml; charset=ISO-8859-1');
		} elseif ( $extFile == 'txt' ){
			header('Content-Type: text/plain');
		} elseif ( $extFile == 'xml' ){
			header('Content-Type: text/xml');
		}
		ob_start();
		require_once $cacheFilePath;
		$render = ob_get_clean();
		echo $render ;
		unlink($cacheFilePath);
		$this->deleteAssetsFile();
		exit;
	}

	public function checkFileISMoldOrNOt($File,$app){
//		$fileName = array_pop(explode(DIRECTORY_SEPARATOR,$File) );
		$fileExtOne = File::ext_file($File);
		$fileExtTwo = File::ext_file(strings::deleteWordLastString($File,'.'.$fileExtOne) );
		if ( strings::strLastHas($app,':plugin') ) {
			$fileExtTree = File::ext_file(strings::deleteWordLastString($File, '.' . $fileExtTwo.'.'.$fileExtOne));
			if ( $fileExtTree != strings::deleteWordLastString($app,':plugin') )
				die('Just Can Load Mold File that name is ended with .'.strings::deleteWordLastString($app,':plugin').'.mold.'.$fileExtOne.' ! file path : ' . $File);
		}
		if ( $fileExtTwo == 'mold' )
			return true ;
		// TODO: send exception and delete all cache of this file
		die('Just Can Load Mold File ! file path : ' . $File);
	}

	private function deleteAssetsFile(){
		$otherFiles = File::get_files(\app::getAppPath('cache/theme/assets'),['.htaccess','index.html']);
		foreach ( $otherFiles as $unlinkFiles )
			if ( time() - File::file_time($unlinkFiles) >= 10*60 )
				unlink($unlinkFiles);
	}

	/**
	 * @param bool $minifyHtml
	 */
	public function setMinifyHtml($minifyHtml) {
		$this->minifyHtml = $minifyHtml;
	}

	/**
	 * @param bool $cacheLifeTime
	 */
	public function setCacheLifeTime($cacheLifeTime) {
		$this->cacheLifeTime = $cacheLifeTime;
	}


	private function decodeCacheProperties($data , $isAssets = false ){
		$cacheFilePath = $this->generateCacheFilePath($data) ;
		if ( !isset($data['version']) or !isset($data['lifeTime']) or !isset($data['file']) or !isset($data['app']) or !isset($data['path']) or !isset($data['parentSize']) or !isset($data['parentCreatedAt']) ) {
			if ( ! $isAssets )
				$this->compileFile($data, $cacheFilePath , true);
			return false ;
		} elseif ( $data['version'] != self::version ){
			if (!  $isAssets )
				$this->compileFile($data, $cacheFilePath ,true);
			return false ;
		} elseif ( time() - $data['createdAt'] > $data['lifeTime'] ){
			if ( ! $isAssets )
				$this->compileFile($data, $cacheFilePath ,true );
			return false ;
		} elseif ( $data['parentCreatedAt'] != File::file_time($data['path'])  ){
			if (!  $isAssets )
				$this->compileFile($data, $cacheFilePath ,true );
			return false ;
		}elseif ( $data['parentSize'] != File::file_size($data['path'])  ){
			if ( ! $isAssets )
				$this->compileFile($data, $cacheFilePath ,true );
			return false ;
		}
		return true ;
	}

	public function generateFuncName($path){
		return 'contact_'.md5($path) ;
	}

	private function generateCacheFilePath($currentTemplate){
		return $this->directorySeparator(\app::getAppPath('cache/theme/',$currentTemplate['app']).md5($currentTemplate['path']).'.'.$currentTemplate['file'].'.php' ) ;
	}

	private function directorySeparator($directory){
		return str_replace(['/', '\\', '>',DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR], DIRECTORY_SEPARATOR, $directory);
	}

	private function Minify_Html($Html)
	{
		$Search = array(
			'/(\n|^)(\x20+|\t)/',
			'/(\n|^)\/\/(.*?)(\n|$)/',
			'/\n/',
			'/\<\!--.*?-->/',
			'/(\x20+|\t)/', # Delete multispace (Without \n)
			'/\>\s+\</', # strip whitespaces between tags
			'/(\"|\')\s+\>/', # strip whitespaces between quotation ("') and end tags
			'/=\s+(\"|\')/'); # strip whitespaces between = "'

		$Replace = array(
			"\n",
			"\n",
			" ",
			"",
			" ",
			"><",
			"$1>",
			"=$1");

		$Html = preg_replace($Search,$Replace,$Html);
		return $Html;
	}

}