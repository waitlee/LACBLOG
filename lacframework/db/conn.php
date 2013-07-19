<?php
namespace LAC\db;

use \PDO;
class Conn extends PDO
{
	public function __construct($conf)
	{
		$ds = $conf->drive . ":dbname=" . $conf->dbname . ";host=" . $conf->host;
		parent::__construct($ds, $conf->user, $conf->password);
	}
}