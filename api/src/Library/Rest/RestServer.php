<?php

namespace Library\Rest;

// GET  	https://url/server.php/data     
// POST     https://url/server.php/data 	body:toto
// PUT      https://url/server.php/data     body:toto,titi
// DELETE   https://url/server.php/data     body:titi

// GET  	https://url/server.php?methode=data     
// POST     https://url/server.php/data 	body:method=data&word=toto
// PUT      https://url/server.php/data     body:method=data&oldword=toto&newword=titi
// DELETE   https://url/server.php/data     body:method=data&word=titi



class RestServer{

	private $service;
	private $httpMethod;
	private $classMethod;
	private $paramRequest;
	private $clientUserAgent;
	private $clientHttpAccept;
	private $json;

	public function __construct(){

		header("Content-type: application/json");

		// stdClass, creation d'un objet vide dans lequel on met ce qu'on veut
		$this->json = new \stdClass();
		$this->json->response 			= "";
		$this->json->apiError			= false;
		$this->json->apiErrorMessage	= "";
		$this->json->serverError 		= false;
		$this->json->serverErrorMessage = "";

		$this->httpMethod 				= strtoupper($_SERVER["REQUEST_METHOD"]);
		$this->clientUserAgent			= $_SERVER["HTTP_USER_AGENT"];
		$this->clientHttpAccept			= $_SERVER['HTTP_ACCEPT'];

		$P = array();
		switch ($this->httpMethod) {
			case 'GET'		: $P = $_GET; break;
			case 'POST'		: 
			case 'PUT'		:
			case 'DELETE'	: parse_str(file_get_contents('php://input'), $P);break;
			default 		: $this->showErrorMessage("Method HTTP Not found");
		}

		if(isset($P['method'])){
			$class = '\Application\Controllers\\' . ucfirst(strtolower($P['method']));
			
			if(file_exists(APP_ROOT . 'Controllers\\' . ucfirst(strtolower($P['method'])) . '.php') && class_exists($class)){
				$this->service = new $class();
			}else{
				$this->showErrorMessage("Service Not found");	
			}
			
			$this->classMethod = strtolower($this->httpMethod);
			if(!method_exists($this->service, $this->classMethod)){
				$this->showErrorMessage("Class Method Not found");
			}
			unset($P['method']);
			$this->paramRequest = $P;
		}else{
			$this->showErrorMessage("Param Method Not found");
		}
	}

	public function showErrorMessage($message){
		$this->json->serverError 		= true;
		$this->json->serverErrorMessage = $message;
		exit;
	}

	//methode qui retourne le resultat final
	public function handle(){
		$res = call_user_func(array($this->service, $this->classMethod), $this->paramRequest);
		$this->json->response 			= $res->response;
		$this->json->apiError			= $res->apiError;
		$this->json->apiErrorMessage	= $res->apiErrorMessage;
		exit;
	}

	public function __destruct(){
		echo json_encode($this->json, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK);
	}
}