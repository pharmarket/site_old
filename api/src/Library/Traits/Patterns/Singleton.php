<?php

namespace Library\Traits\Patterns;

trait Singleton {
	
	private static $instance = NULL;

	public static function getInstance(){
		if (is_null(self::$instance)){
			self::$instance = new self();
		}
		return self::$instance;
	}
}