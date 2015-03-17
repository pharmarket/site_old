<?php

namespace Library\Model;

class Connexion{

	use \Library\Traits\Patterns\Singleton;

	private static $connexions;

	private function __construct(){

	}

	public static function getListConnexionsName(){
		return array_keys(self::$connexions);
	}

	public static function getConnexion($connexionName){
		return self::$connexions[$connexionName];
	}

	public static function addConnexion($connexionName, $connexionPDO){
		self::$connexions[$connexionName] = $connexionPDO;
	}

	public static function connectDB($host 	 = DB_HOST,
									 $dbname = DB_NAME,
									 $user	 = DB_USER,
									 $pass	 = DB_PASS,
									 $char	 = DB_CHAR){
		$database = new \PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
		$database->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
		$database->exec("SET CHARACTER SET $char");
		return $database;
	}
}