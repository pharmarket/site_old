<?php

	#l'autoloader permet de pas avoir à faire require_once à chaque fois
	#on le fait juste une fois pour l'autoloader
	
	session_start();

	require_once("../Library/Loader/Autoloader.php");
	$autoload = \Library\Loader\Autoloader::getInstance();
	$autoload::setBasePath(str_replace("Public", "", __DIR__));

	\Application\Configs\Settings::getInstance();

	try{
		$db = \Library\Model\Connexion::getInstance();
		$db::addConnexion('localhost', $db::connectDB());
	}catch(Execption $e){
		echo "<pre>";
		print_r($e);
		echo "</pre>";
		//var_dump($e->(Execption $e));
	}
	

	//$router = \Library\Router\Router::getInstance();
	//$router::dispatchPage($_GET['page']); 
	$restServer = new \Library\Rest\RestServer();
	$restServer->handle();