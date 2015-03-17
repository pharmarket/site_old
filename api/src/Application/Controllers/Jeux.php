<?php

namespace Application\Controllers;

class Jeux extends \Library\Controller\Controller{

	public function __construct(){
		parent::__construct();
	}

	public function get($param){
		
		$jeux = new \Application\Models\Jeux("localhost");
		$listJeux = (!empty($param['id']))?$jeux->findByPrimary($param['id']):$jeux->fetchAll(); 
		return $this->setApiResult($listJeux);
	}
	
	public function post($param){
		
		return $this->setApiResult("ok Post");
		
	}
	
	public function put($param){
		return $this->setApiResult("ok Put");
		
	}
	
	public function delete($param){

		return $this->setApiResult("ok Delete");
	}
}