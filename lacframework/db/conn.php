<?php
namespace LAC\db;

use \PDO;
use \PDOException;
use \Exception;
class Conn
{
	private $_pdo;
	private $_conf;

	/**
	 * 初始化实例
	 * 
	 * @param object $conf 配置文件数据库相关对象信息
	 */
	public function __construct($conf)
	{
		$ds = $conf->drive . ":dbname=" . $conf->dbname . ";host=" . $conf->host;
		$this->_conf = $conf;
		try {
			$this->_pdo = new PDO($ds, $conf->user, $conf->password);
		} catch (PDOException $e) {
			throw new PDOException($e->getMessage());
		}
		$this->setCharSet();
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
	public function getTabFields($tablename)
	{
		$sql = sprintf("SHOW FIELDS FROM %s", $tablename);
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


}