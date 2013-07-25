<?php
namespace LAC\db;

use \PDO;
use \PDOException;
use \Exception;
class Conn
{
	private $_pdo;
	private $_conf;
	private $_table;
	private $_sql;
	private $_fields;

	/**
	 * 初始化实例
	 * 
	 * @param object $conf 配置文件数据库相关对象信息
	 */
	public function __construct($conf, $table)
	{
		$ds = $conf->drive . ":dbname=" . $conf->dbname . ";host=" . $conf->host;
		$this->_conf = $conf;
		try {
			$this->_pdo = new PDO($ds, $conf->user, $conf->password);
		} catch (PDOException $e) {
			throw new PDOException($e->getMessage());
		}
		$this->setCharSet();
		$this->verifyTable($table);
		$this->_table = $table;
	}

	/**
	 * 设置数据库字符集
	 */
	protected function setCharSet()
	{
		$sql = sprintf("SET NAMES %s", $this->_conf->charset);
		$this->_pdo->exec($sql);
	}

	/**
	 * 获取数据表的字段信息
	 * 
	 * @param  string $tablename 表名
	 * 
	 * @return array            
	 */
	public function getTabFields()
	{
		$sql = sprintf("SHOW FIELDS FROM %s", $this->_table);
		$result = $this->_pdo->query($sql);
		$fields = array();

		foreach ($result as $row) {
			if ($row['Key'] == 'PRI') {
				$fields['pri'] = $row['Field'];
			} else {
				$fields[] = $row['Field'];
			}
		}
		return $fields;
	}

	/**
	 * 获取当前数据库的所有的表集合
	 * 
	 * @return array
	 */
	public function getTabs()
	{
		$tables = array();
		$sql = sprintf("SHOW TABLES FROM %s", $this->_conf->dbname);
		$result = $this->_pdo->query($sql);
		foreach ($result as $row) {
			$tables[] = $row[0];
		}
		return $tables;
	}

	/**
	 * 判断数据表是否存在
	 * 
	 * @param  string $table 表名
	 * 
	 * @return boolean        
	 */
	protected function verifyTable($table)
	{
		if (!in_array($table, $this->getTabs())) {
			throw new Exception("{$table} not found in this database");
		}
		return true;
	}

	/**
	 * 将数组格式的表的字段名变成字符串形式
	 * 
	 * @return string  mysql 语句可使用的字段格式 
	 */
	protected function fieldsToString()
	{
		$fieldsStr = '';
		$fieldsArr = $this->getTabFields();
		foreach ($fieldsArr as $key => $value) {
			$fieldsStr .= $value . ",";
		}
		return rtrim($fieldsStr, ',');
	}

	/**
	 * 初始化查询语句
	 * 
	 * @param  array  $fields 将要查询的字段
	 * 
	 * @return  conn 
	 */
	public function select($fields = NULL)
	{
		if ($fields === NULL) {
			$fields = $this->fieldsToString();
		}

		if (!is_string($fields)) {
			throw new Exception("select() param must be a string");
		}
		$this->_sql = sprintf("SELECT %s FROM %s", $fields, $this->_table);
		return $this;
	}

	/**
	 * 逻辑判断
	 * 
	 * @param  string $field    字段
	 * @param  string $operator 条件运算符
	 * @param  string $value    值
	 * @param  string $type     逻辑类型 AND或者OR，默认为普通where查询
	 * 
	 * @return conn           
	 */
	public function where($field, $operator, $value, $type=NULL)
	{
		if (!in_array($field, $this->getTabFields())) {
			throw new Exception("undefind field {$field}");
		}


		$whereSql = 'WHERE ';
		$operator = strtoupper($operator);
		$allowOperator =  array('=', '>=', '<=', '>', '<', '<>', 'IN', 'LIKE', 'NOT IN', 'BETWEEN');
		if (!in_array($operator, $allowOperator)) {
			throw new Exception("undefind operator {$operator}");
		}

		switch ($operator) {
			case 'IN':
				if (!is_array($value)) {
					throw new Exception("IN action param 3 must be array");
				}
				$value = "(" . implode(',', $value) . ")";
				break;
			case 'NOT IN':
				if (!is_array($value)) {
					throw new Exception("NOT IN param 3 must be array");
				}
				$value = "(" . implode(',', $value) . ")";
				break;
			case 'BETWEEN':
				if (!is_array($value) || count($value) != 2) {
					throw new Exception("BETWEEN param 3 must be array");
				}
				$value = "{$value[0]} AND {$value[1]}";
				break;
			default:
				$value ="'" . $value . "'";
				break;
		}

		if ($type == NULL) {
			if (strpos($this->_sql, 'WHERE')) {
				throw new Exception("function where() can only be used once, you can add type \"AND\" or \" OR\"");
			}
			$whereSql .= $field . ' ' . $operator . ' ' . $value;
		} else {
			$whereSql = strtoupper($type) . ' ' . $field . ' ' . $operator . ' ' . $value;
		} 
		$this->_sql .= ' ' . $whereSql;
		return $this;
	}

	/**
	 * limit 
	 * @param  integer  $limit  查询条数
	 * @param  integer $offSet  起始位置
	 * 
	 * @return conn          
	 */
	public function limit($limit, $offSet = 0)
	{
		if (!is_int($offSet) || !is_int($limit)) {
			throw new Exception("linit() param must be a int");
		}

		$limitSql = sprintf("LIMIT %s,%s", $offSet, $limit);
		$this->_sql .= " " . $limitSql;
		return $this;
	}

	/**
	 * 排序
	 * 
	 * @param  string $field 字段名
	 * @param  string $type  排序类型 默认为降序DESC
	 * 
	 * @return conn
	 */
	public function orderBy($field, $type = 'DESC')
	{
		if (strpos($this->_sql, 'ORDER') > 0 ) {
			$obSql = sprintf(",%s %s", $field, $type);
		} else {
			$obSql = sprintf("ORDER BY %s %s", $field, $type);
		}
		
		$this->_sql .= ' ' . $obSql;
		return $this;
	}

	/**
	 * 分组
	 * 
	 * @param  string $field 字段名
	 * 
	 * @return conn        
	 */
	public function groupBy($field = NULL)
	{
		if ($field === NULL) {
			throw new Exception("groupBy() param missed");
		}
		$groupSql = sprintf("GROUP BY %s", $field);
		$this->_sql .= ' ' . $groupSql;
		return $this;
	}

	/**
	 * 设置表达字段和值一一对应
	 * 
	 * @param string $field 字段名
	 * @param string $value 字段的值
	 */
	public function setValue($field, $value)
	{
		if (!in_array($field, $this->getTabFields())) {
			throw new Exception("field : {$field} not found in table {$this->_table}");
		}

		$this->_fields[$field] = $value;
		return $this;
	}

	/**
	 * 保存数据
	 * 
	 * @return mixed 
	 */
	public function save()
	{
		if ($this->_fields === array()) {
			throw new Exception("No thing to insert");
		}

		$fields = array_keys($this->_fields);
		$value = array_values($this->_fields);
		var_dump($fields);
		var_dump($value);
		$fieldsStr = "(`" . implode('`,`', $fields) . "`)";
		$valueStr = "('" . implode("','", $value) . "')";
		$sql = sprintf("INSERT INTO %s %s VALUES %s", $this->_table, $fieldsStr, $valueStr);
		try {
			$row = $this->_pdo->exec($sql);
		} catch (PDOException $e) {
			throw new PDOException($e->getMessage());
		}

		if ($row > 0) {
			$this->_clear();
			return $this->_pdo->lastInsertId();
		} 
		return false;
	}

	/**
	 * 更新数据
	 * 
	 * @param  string $condition 更新条件
	 * 
	 * @return boolean            
	 */
	public function update($condition)
	{
		$sqlStr = '';
		if ($this->_fields === array()) {
			throw new Exception("No thing to update");
		}

		foreach ($this->_fields as $key => $value) {
			$sqlStr .= "`" . $key . "`='" . $value . "',";
		}

		$str = rtrim($sqlStr, ',');

		$sql = sprintf("UPDATE %s SET %s WHERE %s", $this->_table, $str, $condition);
		try {
			$row = $this->_pdo->exec($sql);
		} catch (PDOException $e) {
			throw new PDOException($e->getMessage());
		}

		if ($row > 0) {
			$this->_clear();
			return true;
		}
		return false;
	}

	public function delete()
	{
		$this->_sql = '';
		$this->_sql = sprintf("DELETE FROM %s ", $this->_table);
		return $this;
	}

	/**
	 * 重置 this->_fields
	 * 
	 * @return  void
	 */
	private function _clear()
	{
		$this->_fields = array();
		$this->_sql = '';
	}

	/**
	 * 执行一条无查询数据
	 * 
	 * @return  
	 */
	public function exec()
	{
		try {
			$row = $this->_pdo->exec($this->_sql);
		} catch (PDOException $e) {
			throw new PDOException($e->getMessage());
		}
		return $row;
	}

	/**
	 * 执行一条数据查询
	 * 
	 * @param  string $type PDO fetch_style 返回一个索引为结果集列名的数组
	 * 
	 * @return array       
	 */
	public function query($type = PDO::FETCH_ASSOC)
	{
		$result = $this->_pdo->query($this->_sql);
		if ($result === false) {
			return array();
		}
		$result = $result->fetch($type);
		$this->_clear();
		return $result;
	}

	/**
	 * 执行数据查询，返回结果集
	 * 
	 * @param  string $type PDO fetch_style 
	 * 
	 * @return array       
	 */
	public function queryAll($type = PDO::FETCH_ASSOC)
	{
		$result = $this->_pdo->query($this->_sql);
		if ($result === false) {
			return array();
		}
		$result = $result->fetchAll($type);
		$this->_clear();
		return $result;
	}
}