<?php

namespace Application\Configs;

class Settings {

	#singleton
	#on a mis singleton en privée donc on est obligé de l'instancier
	#self permet d'acceder à une methode statique

	use \Library\Traits\Patterns\Singleton;

	private function __construct(){
		//phpinfo();
		define("WEB_ROOT", str_replace("index.php", "", $_SERVER["SCRIPT_NAME"]));
		define("LINK_ROOT", str_replace("Public/index.php", "", $_SERVER["SCRIPT_NAME"]));
		define("APP_ROOT", str_replace("Public/index.php", "Application/", $_SERVER["SCRIPT_FILENAME"]));
		define("LIB_ROOT", str_replace("Public/index.php", "Library/", $_SERVER["SCRIPT_FILENAME"]));

		//var_dump(WEB_ROOT, LINK_ROOT, APP_ROOT, LIB_ROOT);

		define("DB_HOST", "localhost");
		define("DB_NAME", "gamenote"); 
		define("DB_USER", "root"); 
		define("DB_PASS", "");
		define("DB_CHAR", "utf8");  
	}
}