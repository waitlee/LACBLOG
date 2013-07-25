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

	/**
	 * 获取所有符合条件的数据
	 * 
	 * @param  string $field     条件字段
	 * @param  string $operator  条件运算符
	 * @param  string $value     条件值
	 * @param  string $select    要查询的字段
	 * 
	 * @return array           
	 */
	public function findAll($field, $operator, $value, $select = NULL)
	{
		$result = $this->conn->select($select)->where($field, $operator, $value)->queryAll();
		return $result;
	}

	/**
	 * 插入数据
	 * 
	 * @param  array  $data 将要插入的数据格式为 array($field => $value, $field1 => $value1 )
	 * 
	 * @return integer  last_insert_id
	 */
	public function insert($data = array())
	{
		if (!is_array($data)) {
			throw new Exception("action insert() must be a array");
		}

		foreach ($data as $key => $value) {
			$this->conn->setValue($key, $value);
		}

		return $this->conn->save();
	}

	/**
	 * 更新数据
	 * 
	 * @param  array  $data      要更新的字段和字段值格式为 array($field=>$value,$field1=>$value1)
	 * @param  string $condition 更新条件
	 * 
	 * @return boolean            
	 */
	public function update($data = array(), $condition = '')
	{
		if (!is_array($data)) {
			throw new Exception("action uodate() 1 param must be a array");
		}

		if ($condition === '') {
			throw new Exception("action missed param condition");
		}

		foreach ($data as $key => $value) {
			$this->conn->setValue($key, $value);
		}

		return $this->conn->update($condition);
	}

	public function delete($field, $operator, $value)
	{
		return $this->conn->delete()->where($field, $operator, $value)->exec();
	}

	public function findBySql()
	{

	}
}