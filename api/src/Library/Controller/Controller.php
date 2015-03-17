<?php

namespace Library\Controller;

abstract class Controller {

	private $responseHeader = "application/json";
	private $apiResult;
			
	public function __construct(){
		
		$this->apiResult					= new \stdClass();
		$this->apiResult->response 			= "";
		$this->apiResult->apiError			= false;
		$this->apiResult->apiErrorMessage	= "";
	}
	
	protected function setApiResult($result, $apiError=false, $apiErrorMessage=""){
		$this->apiResult->response 			= $result;
		$this->apiResult->apiError			= $apiError;
		$this->apiResult->apiErrorMessage	= $apiErrorMessage;
		return $this->apiResult;
	}

	protected function setResponseHeader($value){
		$possibilities = array("txt" => "text/plain",
							   "html" => "text/html",
							   "css" =>"text/css",
							   "js" =>"application/javascript",
							   "json" => "application/json",
							   "xml" => "application/xml"
							   );

		if (array_key_exists(strtolower($value), $possibilities)){
			$this->responseHeader = $possibilities[strtolower($value)];
			return true;
		}
		return false;
	}

	protected function getResponseHeader(){
		return $this->responseHeader;
	}
}