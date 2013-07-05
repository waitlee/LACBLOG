<?php
// namespace LAC;
use \LAC\webHttpapplication;
use \Exception;

class LAC
{
	private static $_w;
	private static $_c;

	public static function autoload($className)
	{
		$classInfo = explode("\\", $className);
		$classFlag = array_shift($classInfo);
		$classBaseName = strtolower(array_pop($classInfo)) . ".php";

		$classPath = "";
		foreach ($classInfo as $value) {
			$classPath = $value . DS;
		}
		$preFix = self::getPreFix($classFlag);

		$classRealPath = $preFix . $classPath . $classBaseName;

		if (file_exists($classRealPath)) {
			self::$_c[$className] = $classRealPath;
			require $classRealPath;
		}else{
			throw new Exception("file {$classBaseName} not fount in");
			
		}
	}


	public static function createWebApplication($config = NULL)
	{
		if (self::$_w == NULL) {
			$_w = new \LAC\core\webHttpapplication($config);
		}
	}

	private static function getPreFix($classFlag)
	{
		switch ($classFlag) {
			case 'LAC':
				$preFix = F_PATH;
				break;

			case 'APP':
				$preFix = APP_PATH;
				break;

			case 'ROOT':
				$preFix = ROOT_PATH;
				break;

			default:
				$preFix = F_PATH;
				break;

			return $preFix . DS;
		}
	}

	public function run()
	{
		var_dump($_GET);
		echo 'run';
	}
}

spl_autoload_register(array('LAC', 'autoload'));
