<?php

class RestClient{

	private $userAgent;
	private $requestUrl;
	private $requestBody;
	private $responseBody;
	private $responseHeader;

	public function __construct($serverUrl){

		if(!isset($_SESSION)){
			session_start();
		}

		if(!filter_var($serverUrl, FILTER_VALIDATE_URL)){
			throw new Exception("Error server url not valid");	
		}

		$this->userAgent 		= "RestClient 1.0";
		$this->requestUrl 		= $serverUrl;
		$this->requestBody 		= null;
		$this->responseBody 	= null;
		$this->responseHeader 	= null;
	}

	public function setUserAgent($value){
		if(!empty($value)){
			$this->userAgent = $value;
		}
	}

	public function query($httpMethod="GET", $request=null){

		$this->responseBody 	= null;
		$this->responseHeader 	= null;

		if(is_null($request)){
			throw new Exception("Error Request is not valid");
		}
		$this->requestBody = $request;

		$ch = curl_init($this->requestUrl);
		switch (strtoupper($httpMethod)) {
			case 'GET'   : $this->methodGet($ch); break;
			case 'POST'  : $this->methodPost($ch); break;
			case 'PUT'   : $this->methodPut($ch); break;
			case 'DELETE': $this->methodDelte($ch); break;
			default      : throw new Exception("Error Http Method is not valid");
		}
		return $this->responseBody;
	}

	//& permet de faire une référence mémoire
	protected function executeRequest(&$ch){

		$strCookie = session_name(). '=' .session_id();
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: Application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $strCookie);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $strCookie);
		curl_setopt($ch, CURLOPT_COOKIESESSION, false);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);

		$this->responseBody 	= curl_exec($ch);
		$this->responseHeader 	= curl_getinfo($ch);
		curl_close($ch);
 	}

	protected function methodGet(&$ch){

		curl_setopt($ch, CURLOPT_URL, $this->requestUrl . "?" . $this->requestBody);
		$this->executeRequest($ch);
	}

	protected function methodPost(&$ch){

		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->requestBody);
		curl_setopt($ch, CURLOPT_POST, true);
		$this->executeRequest($ch);
	}

	protected function methodPut(&$ch){

		$f = fopen('php://temp', 'rw');
		fwrite($f, $this->requestBody);
		rewind($f);

		curl_setopt($ch, CURLOPT_INFILE, $f);
		curl_setopt($ch, CURLOPT_INFILESIZE, strlen($this->requestBody));
		curl_setopt($ch, CURLOPT_PUT, true);
		$this->executeRequest($ch);
		fclose($f);
	}

	protected function methodDelte(&$ch){

		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->requestBody);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		$this->executeRequest($ch);
	}
}