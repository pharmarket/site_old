<?php

namespace Application\Models;

class Jeux extends \Library\Model\Model{

	protected $table 	= "jeux";
	protected $primary 	= "id_jeux";

	public function __construct($connexionName){
		parent::__construct($connexionName);
	}
}