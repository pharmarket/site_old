<?php

namespace Library\Model;

abstract class Model{

	private $database;
	protected $table;
	protected $primary;

	public function __construct($connexionName){

		$classConnexion = \Library\Model\Connexion::getInstance();
		$this->database = $classConnexion::getConnexion($connexionName);
	}

	public function findByPrimary($valuePrimary, $field="*"){
		$sql = $this->database->prepare("SELECT $field FROM `{$this->table}` WHERE `{$this->primary}`=:primary");
		$sql->execute(array("primary"=>$valuePrimary));
		return $sql->fetchAll();
	}

	public function fetchAll($where=1, $fields="*"){
		$sql = $this->database->prepare("SELECT $fields FROM `{$this->table}` WHERE $where");
		$sql->execute();
		return $sql->fetchAll();
	}

	/**
	 *
	 *$data = array("titre"->"blabla",)
	 *				"contenu"->".....");
	*/
	public function insert($data){

		// `titre`,`contenu`
		$listFields = "`".implode("`,`", array_keys($data))."`";

		// :titre, :contenu
		$listValues = ":".implode(",:", array_keys($data));

		$sql = $this->database->prepare("INSERT INTO `{$this->table}` ($listFields) VALUES ($listValues)");
		return $sql->execute($data);
	}

	/**
	 *
	 *$data = array("id"->"1",)
	 *				"titre"->"blabla"
	 *				"contenu"->"....");
	*/
	public function updateByField($fieldForUpdate, $data){
		$list = $this->createListForUpdate($data, $fieldForUpdate);
		$sql = $this->database->prepare("UPDATE `{$this->table}` SET $list WHERE `$fieldForUpdate`=:$fieldForUpdate");
		return $this->returnAffectedRowBool($sql->execute($data)); 
	}

	public function update($where, $data){
		$list = $this->createListForUpdate($data);
		$sql = $this->database->prepare("UPDATE `{$this->table}` SET $list WHERE $where");
		//return $this->returnAffectedRowBool($sql->execute($data));
		return $sql->execute($data);
	}

	protected function createListForUpdate($data, $exclude=""){
		$list="";
		foreach ($data as $key => $value) {
			if($key == $exclude){
				continue;
			}
			$list .= "`$key`=:$key,";
		}
		return  substr($list, 0, -1);	
	}

	protected function returnAffectedRowBool($query){
		if($query){
			if($query->rowcount()>0){
				return true;
			}
		}
		return false;
	}

	public function deleteByPrimary($valuePrimary){
		$sql = $this->database->prepare("DELETE FROM `{$this->table}` WHERE `{$this->primary}`=:primary");
		return $sql->execute(array("primary"=>$valuePrimary));
	}

	public function delete($where){
		$sql = $this->database->prepare("DELETE FROM `{$this->table}` WHERE $where");
		return $sql->execute();
	}
}