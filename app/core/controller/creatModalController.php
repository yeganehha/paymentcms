<?php


namespace App\Controller;

/**
 * Created by PhpStorm.
 * User: Erfan
 * Date: 4/7/2017
 * Time: 10:00 PM
 */
class creatModal extends \controller {
	public $text ;
	private $tableName ;
	private $implementsClass ;
	private $minFile;
	private $columns ;
	private $keyTable;

	/**
	 * creatTable constructor.
	 *
	 * @param $db
	 */
	public function __construct( $minFile = false ) {
		$this->columns = null ;
		$this->text = null ;
		$this->minFile = $minFile ;
		return true ;
	}

	/**
	 * @param null $text
	 */
	public function copyRight($text = null , $minFile = false ){
		if ( $text == null ){
			$text = $this->newLine($minFile) .$this->newLine($minFile) .'// *************************************************************************'.$this->newLine($minFile) .'// *                                                                       *'.$this->newLine($minFile) .'// * TableClass - The Complete Table To Class PHP Function                 *'.$this->newLine($minFile) .'// * Copyright (c) Erfan Ebrahimi. All Rights Reserved,                    *'.$this->newLine($minFile) .'// * BuildId: 1                                                            *'.$this->newLine($minFile) .'// *                                                                       *'.$this->newLine($minFile) .'// *************************************************************************'.$this->newLine($minFile) .'// *                                                                       *'.$this->newLine($minFile) .'// * Email: persionhost@gmail.com                                          *'.$this->newLine($minFile) .'// * phone: 09361090413                                                    *'.$this->newLine($minFile) .'// *                                                                       *'.$this->newLine($minFile) .'// *                                                                       *'.$this->newLine($minFile) .'// *************************************************************************'.$this->newLine($minFile) .$this->newLine($minFile) .$this->newLine($minFile) ;
		}
		$this->text .= $text ;
	}

	public function setNameTable( $tableName = null , $keyTable = 'id', $implementssClass = null ){
		$this->tableName  = $tableName;
		$this->keyTable  = $keyTable;
		$this->implementsClass  = str_replace('-' ,'\\' , $implementssClass);
		return true ;
	}

	/**
	 * @return bool
	 */
	public function firstLineOfFiel (){
		$this->text .= '<?php '.$this->newLine().$this->newLine();
		return true;
	}

	public function nameSpaceAdd($nameSpace){
		$this->text .= 'namespace '.$nameSpace.';'.$this->newLine().$this->newLine();
		return true;
	}
	public function createClass(){
		//$this->text .= $this->newLine().'if ( ! class_exists(\''.$this->tableName.'\')) {'.$this->newLine( );
		if ( $this->implementsClass != null  )
			$this->text .= $this->newLine().'use '.$this->implementsClass.' ;'.$this->newLine( );
		$this->text .= $this->newLine().'class '.$this->tableName.( $this->implementsClass != null ? ' implements '.$this->implementsClass : '').' {'.$this->newLine().$this->newLine();
		return true;
	}

	public function creatVariable( ){
		$columns = $this->columns();
		//$this->text .= $this->newLine().'	private $db ;';
		foreach ( $columns as $key => $column)
			$this->text .= $this->newLine().'	private $'.$column['Field'].' ;';

		return true ;
	}

	public function creatGetter( ){
		$columns = $this->columns();
		foreach ( $columns as $key => $column) {
			$this->text .= $this->newLine() . '	public function get' . ucfirst($column['Field']) . '() {' . $this->newLine();
			$this->text .= '		return $this->'.$column['Field']. ' ;' . $this->newLine();
			$this->text .= '	}' . $this->newLine(). $this->newLine();
		}
		return true ;
	}


	public function creatSetter( ){
		$columns = $this->columns();
		foreach ( $columns as $key => $column) {
			$this->text .= $this->newLine() . '	public function set' . ucfirst($column['Field']) . '( $' . $column['Field'] . ' = null ) {' . $this->newLine();
			$this->text .= '		$this->'.$column['Field']. ' = $'.$column['Field'].' ;' . $this->newLine();
			$this->text .= '	}' . $this->newLine(). $this->newLine();
		}
		return true ;
	}

	public function creatUpdator (  ){
		$columns = $this->columns();
		$this->text .= $this->newLine() . '	public function upDateDataBase( ) {' . $this->newLine();
		foreach ( $columns as $key => $column) {
			if ( $column['Field'] != $this->keyTable )
				$this->text .= '		$array[\''.$column['Field'].'\'] = $this->'.$column['Field']. ' ;' . $this->newLine();
		}
		$this->text .= '		if ( \database::update(\''.$this->tableName.'\' , $array , array(\'query\' => \''.$this->keyTable.' = ?\', \'param\' => array($this->'.$this->keyTable.')) ) ) ' . $this->newLine();
		$this->text .= '			return $this->returning() ;' . $this->newLine() ;
		$this->text .= '		return $this->returning(null,false,\''.$this->tableName.'2\') ;' . $this->newLine() ;
		$this->text .= '	}' . $this->newLine(). $this->newLine();
		return true ;
	}


	public function creatReturnAsArray (  ){
		$columns = $this->columns();
		$this->text .= $this->newLine() . '	public function returnAsArray( ) {' . $this->newLine();
		foreach ( $columns as $key => $column) {
			$this->text .= '		$array[\''.$column['Field'].'\'] = $this->'.$column['Field']. ' ;' . $this->newLine();
		}
		$this->text .= '		return $array ;' . $this->newLine() ;
		$this->text .= '	}' . $this->newLine(). $this->newLine();
		return true ;
	}



    public function creatSearch (  ){
		$this->text .= $this->newLine() . '	public function search( $searchVariable, $searchWhereClaus , $tableName = \''.$this->tableName.'\'  , $fields = \'*\' ) {' . $this->newLine();
		$this->text .= '		$results = \database::searche($tableName, $searchWhereClaus, $searchVariable, true ,false,$fields );' . $this->newLine();
		
		//$this->text .= '		$results[\'numbers\'] = count($results);' . $this->newLine() ;
		
		$this->text .= '		return $this->returning($results) ;' . $this->newLine() ;
		$this->text .= '	}' . $this->newLine(). $this->newLine();
		return true ;
	}
	
	
	public function creatInsertor ( ){
		$columns = $this->columns();
		$this->text .= $this->newLine() . '	public function insertToDataBase( ) {' . $this->newLine();
		foreach ( $columns as $key => $column) {
			if ( $column['Field'] != $this->keyTable )
				$this->text .= '		$array[\''.$column['Field'].'\'] = $this->'.$column['Field']. ' ;' . $this->newLine();
		}
		$this->text .= '		$id = \database::insert(\''.$this->tableName.'\' , $array  ); ' . $this->newLine();
		if ( $column['Field'] != $this->keyTable )
		$this->text .= '		$this->'.$this->keyTable .' = $id ; ' . $this->newLine();
		$this->text .= '		if ( $id ) {' . $this->newLine();
		$this->text .= '			$this->'.$this->keyTable .' = $id ; ' . $this->newLine();
		$this->text .= '			return $this->returning($id) ;' . $this->newLine() ;
		$this->text .= '		}' . $this->newLine() ;
		$this->text .= '		return $this->returning(null,false,\''.$this->tableName.'3\') ;' . $this->newLine() ;
		$this->text .= '	}' . $this->newLine(). $this->newLine();
		return true ;
	}

	public function creatDelete ( ){
		$this->text .= $this->newLine() . '	public function deleteFromDataBase( ) {' . $this->newLine();
		$this->text .= '		if ( \database::delete(\''.$this->tableName.'\', array(\'query\' => \''.$this->keyTable.' = ?\', \'param\' => array($this->'.$this->keyTable.')) ) ) ' . $this->newLine();
		$this->text .= '			return $this->returning() ;' . $this->newLine() ;
		$this->text .= '		return  $this->returning(null,false,\''.$this->tableName.'1\') ;' . $this->newLine() ;
		$this->text .= '	}' . $this->newLine(). $this->newLine();
		return true ;
	}


	public function creatCunstructor(){
		$columns = $this->columns();
		//$this->text .= $this->newLine().$this->newLine().'	public function __construct( $db , $searchVariable = null , $searchWhereClaus = \''.$this->keyTable.' = ? \' ){'. $this->newLine();
		$this->text .= $this->newLine().$this->newLine().'	public function __construct(  $searchVariable = null , $searchWhereClaus = \''.$this->keyTable.' = ? \' ){'. $this->newLine();
		//$this->text .= '		/* @var db $db */' . $this->newLine();
		//$this->text .= '		$this->db = $db ;' . $this->newLine();
		$this->text .= '		if ( $searchVariable != null ) {' . $this->newLine();
		$this->text .= '			$result = \database::searche(\''.$this->tableName.'\' ,  $searchWhereClaus  , array($searchVariable) ); ' . $this->newLine();
		$this->text .= '			if ( $result != null ) {' . $this->newLine();
		foreach ( $columns as $key => $column) {
			$this->text .= '				$this->'.$column['Field'].' = $result[\''.$column['Field'].'\'] ;' . $this->newLine();
		}
		$this->text .= '			} else ' . $this->newLine();
		$this->text .= '				return $this->returning(null,false,\''.$this->tableName.'4\');' . $this->newLine();
		$this->text .= '		}' . $this->newLine();
		$this->text .= '		return $this->returning();' . $this->newLine();
		$this->text .= '	}' . $this->newLine();
	}


	public function endLineOfFiel (){
		$this->text .= $this->newLine().$this->newLine().'?>';
		return true;
	}

	public function endMethodOfClassReturnArray (){
		$this->text .= $this->newLine().$this->newLine().'	private function returning($return = null , $status = true , $errorNumber = "'.$this->tableName.'0" , $massagesParams = null ){'.$this->newLine();
		$this->text .= '		$array[\'status\']= $status ;'.$this->newLine().'		$array[\'massagesParams\'] = $massagesParams;'.$this->newLine();
		$this->text .= '		$array[\'result\'] = $return ;'.$this->newLine().'		$array[\'error\'] = $errorNumber ;'.$this->newLine().'		return $array;'.$this->newLine().'	}'.$this->newLine().$this->newLine();
		return true;
	}
	public function endMethodOfClass (){
		$this->text .= $this->newLine().$this->newLine().'	private function returning($return = null , $status = true , $errorNumber = "'.$this->tableName.'0" , $massagesParams = null ){'.$this->newLine();
		$this->text .= '		if ( $return == null )'.$this->newLine().'				return $status ;'.$this->newLine();
		$this->text .= '		else'.$this->newLine().'				return $return ;'.$this->newLine().$this->newLine().'	}'.$this->newLine().$this->newLine();
		return true;
	}

	public function endLineOfClass (){
		$this->text .= $this->newLine()/*.$this->newLine().'}'*/.$this->newLine().'}'.$this->newLine();
		return true;
	}
	public function creatFile(){
		$myfile = fopen(__DIR__ . '/../model/' .$this->tableName.'Model.php', "w") or die("Unable to open config file!");
		if ( !fwrite($myfile, $this->text ) )
			return false ;
		fclose($myfile);
		return true ;
	}

	public function columns( $resetColumns = false ){
		if ( $this->columns == null )
			$this->columns = \database::searche(null , 'SHOW COLUMNS FROM '.$this->tableName , null ,false , false , '*' , false , true );
		if ( $resetColumns )
			$this->columns =  null ;

		return $this->columns ;
	}
	/**
	 * @return string
	 */
	private function newLine( $minFile = false ) {
		if ( $this->minFile && $minFile)
			return ' ';
		return chr(10);
	}

	public function creat($param){
		$this->setNameTable($param[0],$param[1],$param[2]);
		$this->firstLineOfFiel();
		$this->copyRight();
		if ( isset($param[3]))
			$this->nameSpaceAdd(str_replace('-', '\\' ,$param[3]));
		$this->createClass();
		$this->creatVariable();
		$this->creatCunstructor();
		$this->creatSearch();
		$this->creatSetter();
		$this->creatGetter();
		$this->creatInsertor();
		$this->creatUpdator();
		$this->creatDelete();
		$this->creatReturnAsArray();
		//$this->endMethodOfClassReturnArray();
		$this->endMethodOfClass();
		$this->endLineOfClass();
		$this->creatFile();
	}

}