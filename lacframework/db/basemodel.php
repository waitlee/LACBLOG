<?php

namespace LAC\db;

use \LAC\LAC;
use \LAC\db\conn;
use \Exception;

class BaseModel
{
	public static $primaryKey;
	public static $fields;
	protected $conf;
	protected $table;
	protected $pdo;

	public function __construct()
	{
		$this->conf = LAC::app()->conf()->db;
		$this->table = $this->getTableName();
		$this->pdo = new Conn($this->conf);
	}

	public function getTableName()
	{
		return $this->conn;
		//return $this->conf->pre . $this->tableName();
	}

	public function tableName()
	{
		$classInfo = explode('\\', get_class($this));
		$tableName = array_pop($classInfo);
		return $tableName;
	}

	public function findByPk()
	{

	}

	public function findAll()
	{

	}

	public function insert()
	{

	}

	public function delete()
	{

	}

	public function update()
	{

	}

	public function find()
	{

	}
}