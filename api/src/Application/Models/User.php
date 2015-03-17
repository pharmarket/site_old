<?php

namespace Application\Models;

class User extends \Library\Model\Model{

	protected $table 	= "user";
	protected $primary  = "id";

	public function __construct($connexionName){
		parent::__construct($connexionName);
	}
}