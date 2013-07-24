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
		$this->table = $this->getTableName();
		$this->conn = new Conn($this->conf, $this->table);
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
		return $this->conn->getTabFields();
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
		$result = $this->conn->select()->where(self::$primaryKey, '=', $id)->query();
		return $result;
	}

	public function findAll($field, $operator, $value, $select = NULL)
	{
		$result = $this->conn->select($select)->where($field, $operator, $value)->queryAll();
		return $result;
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

	public function findBySql()
	{

	}
}