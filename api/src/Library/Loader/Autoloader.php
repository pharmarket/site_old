<?php

#dès qu'on appelle une class ou un trait , l'Autoloader charge la classe ou le trait en question
namespace Library\Loader;
require_once(str_replace("Loader", "Traits/Patterns/Singleton.php", __DIR__));

class Autoloader {

	use \Library\Traits\Patterns\Singleton;

	private static $basePath = NULL;

	public static function setBasePath($value){
		self::$basePath = $value;
	}

	private function __construct(){
		#enregistre la méthode qu'on va crée
		spl_autoload_register(array(__CLASS__, "autoload"));
	}

	#utilisé dès qu'on fait appel à une classe
	#des qu'on fait appel une classe il utilise la methode autoload
	#fait exeption que quand on fait new quelque chose
	private static function autoload($class){
		if (is_null(self::$basePath)){
			throw new Exception("Autoloader ::basePath is null");
		}
		require_once (self::$basePath . str_replace("\\", DIRECTORY_SEPARATOR, $class) . ".php");
	}
}