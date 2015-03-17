<?php

namespace Application\Controllers;

class Error extends \Library\Controller\Controller{

	public function __construct(){

	}

	public function indexAction(){
		var_dump("ctrl:Error, act:index");
	}
}