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
	protected $conn;

	public function __construct()
	{
		$this->conf = LAC::app()->conf()->db;
		$this->conn = new Conn($this->conf);
		$this->table = $this->getTableName();
		$this->setFields();
	}

	/**
	 * 初始化 fileds 和 primaryKey
	 */
	protected function setFields()
	{
		$fields = $this->getTableFields();
		self::$fields = $fields;
		self::$primaryKey = $fields['pri'];
	}

	/**
	 * 获取带前缀的数据表名称
	 * 
	 * @return string 
	 */
	protected function getTableName()
	{
		$table = $this->conf->pre . $this->tableName();

		if (!in_array($table, $this->getTabs())) {
			throw new Exception(sprintf("table '%s' not found in database '%s'", $this->table, $this->conf->dbname));
		}
		return $table;
	}

	/**
	 * 设置表名
	 * 
	 * @return string 
	 */
	protected function tableName()
	{
		$classInfo = explode('\\', get_class($this));
		$tableName = array_pop($classInfo);
		return $tableName;
	}

	/**
	 * 获取当前表的字段信息
	 * 
	 * @return array 数据表的字段集合
	 */
	protected function getTableFields()
	{
		return $this->conn->getTabFields($this->table);
	}

	/**
	 * 获取当前数据库中数据表的集合
	 * 
	 * @return array 
	 */
	protected function getTabs()
	{
		return $this->conn->getTabs();
	}

	/**
	 * 通过主键查询一条数据
	 * 
	 * @param  int $id 主键id
	 * 
	 * @return  array  查询的结果集  
	 */
	public function findByPk($id)
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