<?php

namespace Application\Controllers;

class Index extends \Library\Controller\Controller{

	public function __construct(){

	}

	public function indexAction(){
		$this->addstyle("flexslider");
		$this->addScript("jquery.flexslider-min");
		$this->addScript("index_index");

		$this->setDataView(array("pageTitle" => "Accueil"));
	}

	public function testAction($nom="", $prenom=""){
		$this->setDataView(array("pageTitle" => "Test"));
	}
}