<?php
/**
 * Created by Jahesh co.
 * User: Erfan Ebrahimi (http://ErfanEbrahimi.ir)
 * Date: 9/18/2017
 * Time: 4:56 PM
 * project : hesabam
 * virsion : 1.0
 * update Time : 9/18/2017 - 4:56 PM
 * Discription of this Page :
 */

if (!defined('paymentCMS')) die('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css"><div class="container" style="margin-top: 20px;"><div id="msg_1" class="alert alert-danger"><strong>Error!</strong> Please do not set the url manually !! </div></div>');


/**
 * @property PDO  DataBase
 */
class database {

	/* @var PDO $DataBase */
	/* @property PDO $DataBase */
	private static $DataBase;  // name of DB Connection
	private static $TabelDbName; // name of tabel first char

	/**
	 * @param $config
	 *
	 * @return bool
	 */
	public static function conection($config) {

		if ($config['_dbName'] != null) {
			try {
				self::$DataBase = new PDO('mysql:host=' . $config['_dbHost'] . ';dbname=' . $config['_dbName'], $config['_dbUsername'], $config['_dbPassword'], array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
				self::$TabelDbName = $config['_dbTableStartWith'];
				return true;
			} catch (PDOException $e) {
				show($e->getMessage());
				return (new database)->error($e->getMessage(), 'connection to db with PDO');
			}
		} else
			return (new database)->error('cant get config', 'connection to db with PDO');
	}

	public static function startTransaction(){
		$query = 'START TRANSACTION;';
		self::fullQuery($query);
	}

	public static function commit(){
		$query = 'COMMIT;';
		self::fullQuery($query);
	}

	/**
	 * @param      $TabelName
	 * @param      $array
	 * @param bool $showId
	 * @param bool $showQuery
	 *
	 * @return bool|null|string
	 */
	public static function insert($TabelName, $array, $showId = true, $showQuery = false) {

		foreach ($array as $key => $value) if ($value === null) unset($array[$key]);

		$query = 'INSERT INTO ' . self::$TabelDbName . $TabelName . ' ( `' . implode("`, `", array_keys($array)) . '` ) VALUES ( :' . implode(", :", array_keys($array)) . ' ) ';

		// TODO: clean this row afther test
		if ($showQuery) {
			die($query);
			return null;
		}

		$stmt = self::$DataBase->prepare($query);
		/*foreach ($array as $key => $value)
			if ($value === null)
				$stmt->bindValue(':'.$key, null, PDO::PARAM_INT);
			else
				$stmt->bindValue(":" . $key, $value);
		*/
		foreach ($array as $key => $value)
			$stmt->bindValue(":" . $key, $value);
		try {
			self::$DataBase->beginTransaction();
			if ($stmt->execute()) {
				if ($showId) {
					$reult = self::$DataBase->lastInsertId();
					self::$DataBase->commit();
					return $reult;
				} else
					return true;
			} else
				return (new database)->error('execute return false.', $query , $array);
		} catch (PDOException $e) {
			return (new database)->error($e->getMessage(), $query , $array);
		}
	}


	/**
	 * @param        $TabelName
	 * @param string $query
	 * @param null   $param
	 * @param bool   $justReturnArray
	 * @param bool   $idInIndexOfRow
	 * @param string $getFilds
	 * @param bool   $showQuery
	 * @param bool   $fullQuery
	 *
	 * @return bool|void
	 */
	public static function searche($TabelName, $query = ' 1 = 1 ', $param = null, $justReturnArray = false, $idInIndexOfRow = false, $getFilds = "*", $showQuery = false, $fullQuery = false) { // searche ind DB ( name of tabel , value of 'where' in query , return array or not , just return id of rows , show query for debuging , if $query isn't for value of 'where'  )

		if ($fullQuery) $setQuery = $query; else {
			$setQuery = "SELECT " . $getFilds . " FROM  " . self::$TabelDbName . $TabelName . " WHERE " . $query; // creat query
		}
		// TODO: clean this row afther test
		if ($showQuery) {
			die($setQuery);
			return;
		}
		try {
			$prepared = self::$DataBase->prepare($setQuery);
			$prepared->execute($param);
			if ($idInIndexOfRow) $result = $prepared->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC); else
				$result = $prepared->fetchAll(PDO::FETCH_ASSOC);

			if ($justReturnArray) return $result;

			if (count($result) == 1) return $result[0];
			return $result;
		} catch (PDOException $e) {
			return (new database)->error($e->getMessage(), $query , $param);
		}
	}


	/**
	 * @param        $TabelName
	 * @param string $query
	 * @param null   $param
	 * @param string $getFilds
	 * @param bool   $showQuery
	 * @param bool   $fullQuery
	 *
	 * @return bool|void
	 */
	public static function counting($TabelName, $query = ' 1 = 1 ', $param = null, $getFilds = "*", $showQuery = false, $fullQuery = false) { // searche ind DB ( name of tabel , value of 'where' in query , return array or not , just return id of rows , show query for debuging , if $query isn't for value of 'where'  )
		if ($fullQuery) $setQuery = $query; else {
			$setQuery = "SELECT count(" . $getFilds . ") as cccC FROM  " . self::$TabelDbName . $TabelName . " WHERE " . $query; // creat query
		}

		// TODO: clean this row afther test
		if ($showQuery) {
			die($setQuery);
			return;
		}
		try {
			$prepared = self::$DataBase->prepare($setQuery);
			$prepared->execute($param);
			$result = $prepared->fetchAll(PDO::FETCH_ASSOC);
			return $result[0]['cccC'];
		} catch (PDOException $e) {
			return (new database)->error($e->getMessage(), $query , $param);
		}
	}


	/**
	 * @param      $TabelName
	 * @param      $query_Set_array
	 * @param      $query_search
	 * @param bool $showQuery
	 *
	 * @return bool|null
	 */
	public static function update($TabelName, $query_Set_array, $query_search, $showQuery = false) { // update row ( name of tabel , value of new Variable  ,  value of 'where' in query , if $query_Set_array is array of information or is string of update query , show query for debuging  )

		//foreach ($query_Set_array as $key => $value) if ($value === null) unset($query_Set_array[$key]);

		foreach ($query_Set_array as $key => $value) {
			$query_Set_array2[] = ' `' . $key . "` = :" . $key . " "; // insert new value of Variable in query from array
		}

		$query_search_exploded = explode('?', $query_search['query']);
		$query_search['query'] = $query_search_exploded[0];
		for ($i = 1; $i < count($query_search_exploded); $i++) $query_search['query'] .= ':EXPLODED' . ($i - 1) . $query_search_exploded[$i];

		$query_Set = implode(' , ', $query_Set_array2);
		$query = 'UPDATE ' . self::$TabelDbName . $TabelName . ' SET ' . $query_Set . ' WHERE ' . $query_search['query'];
		// TODO: clean this row afther test
		if ($showQuery) {
			die($query);
			return null;
		}

		$stmt = self::$DataBase->prepare($query);
		// TODO : null have erro
		foreach ($query_Set_array as $key => $value)
			if ( $value != null )
				$stmt->bindValue(':' . $key, $value);
			else
				$stmt->bindValue(':' . $key, NULL , PDO::PARAM_NULL);

		if (isset($query_search['param']) && is_array($query_search['param']) && count($query_search_exploded) > 0) foreach ($query_search['param'] as $key => $value) $stmt->bindValue(':EXPLODED' . $key, $value);

		try {
			self::$DataBase->beginTransaction();
			if ($stmt->execute()) {
				self::$DataBase->commit();
				return true;
			} else {
				self::$DataBase->commit();
				$erroInfo = self::$DataBase->errorInfo();
				return (new database)->error($erroInfo[2], $query , $query_Set_array);
			}
		} catch (PDOException $e) {
			return (new database)->error($e->getMessage(), $query , $query_Set_array);
		}

	}


	/**
	 * @param      $TabelName
	 * @param      $query_search
	 * @param bool $showQuery
	 *
	 * @return bool|void
	 */
	public static function delete($TabelName, $query_search, $showQuery = false) { // delete row ( name of tabel , value of 'where' in query , show query for debuging  )

		$query_search_exploded = explode('?', $query_search['query']);
		$query_search['query'] = $query_search_exploded[0];
		for ($i = 1; $i < count($query_search_exploded); $i++) $query_search['query'] .= ':EXPLODED' . ($i - 1) . $query_search_exploded[$i];

		$query = "DELETE FROM `" . self::$TabelDbName . $TabelName . "` WHERE " . $query_search['query'] . " ";
		if ($showQuery) {
			die($query);
			return;
		}
		$stmt = self::$DataBase->prepare($query);
		if (isset($query_search['param']) && is_array($query_search['param']) && count($query_search_exploded) > 0) foreach ($query_search['param'] as $key => $value) $stmt->bindValue(':EXPLODED' . $key, $value);

		try {
			self::$DataBase->beginTransaction();
			if ($stmt->execute()) {
				self::$DataBase->commit();
				return true;
			} else {
				self::$DataBase->commit();
				$erroInfo = self::$DataBase->errorInfo();
				return (new database)->error($erroInfo[2], $query , $query_search['param']);
			}
		} catch (PDOException $e) {
			return (new database)->error($e->getMessage(), $query , $query_search['param']);
		}
	}

	/**
	 * @param $query
	 *
	 * @return string
	 */
	public static function fullQueryFetch($query) {

		try {
			$prepared = self::$DataBase->prepare($query);
			$prepared->execute();
			$result = $prepared->fetchAll(PDO::FETCH_ASSOC);
			return $result;
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	/**
	 * @param      $query
	 * @param bool $showQuery
	 *
	 * @return bool|void
	 */
	public static function fullQuery($query, $showQuery = false) { // do full query ( query , show query for debuging  )
		if ($showQuery) {
			die($query);
			return;
		}

		try {
			$stmt = self::$DataBase->prepare($query);
			self::$DataBase->beginTransaction();
			$result = $stmt->execute();
			self::$DataBase->commit();
			if ($result) {
				return $result;
			} else {
				$erroInfo = self::$DataBase->errorInfo();
				return (new database)->error($erroInfo[2], $query);
			}
		} catch (PDOException $e) {
			return (new database)->error($e->getMessage(), $query);
		}
	}


	/**
	 * @return bool
	 */
	public static function close() {

		if (self::$DataBase != null) self::$DataBase = null;
		return true;
	}

	/**
	 * @param      $description
	 * @param null $query
	 * @param null $data
	 *
	 * @return bool
	 */
	private function error($description, $query = null , $data = null ) { // creatt error log file too save mysqli errors

		$massage = "Data Base Error ! " . chr(10) . "    --> descrition : " . $description . " " . chr(10) . "    --> query : " . $query . " " . chr(10). "    --> data : " . json_encode($data) . " " . chr(10);
		$included_files = (array)get_included_files();
		foreach ($included_files as $filename) $massage .= "    --> base On : " . $filename . chr(10);
		error_log($massage);
		/*echo "Data Base Error !";
		exit;*/
		return false;
	}

	/**
	 * @return mixed
	 */
	public static function getDataBase() {
		return self::$DataBase;
	}
}
