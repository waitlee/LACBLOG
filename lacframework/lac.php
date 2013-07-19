<?php

/**
 * LAC 框架核心类
 *
 * @package LAC
 */

namespace LAC;

use \LAC\core\webHttpapplication;
use \Exception;
use \LAC\helper\ChromePhp;


class LAC
{
	private static $_w;
	private static $_c = array();

	/**
	 * 获取web应用
	 * 
	 * @return  object 已经创建好的web应用
	 */
	public static function app()
	{
		return self::$_w;
	}

	/**
	 * 自动加载具有完整命名空间的类文件
	 * 
	 * @param  string $className 具有完整命名空间的类
	 * 
	 * @return void        
	 */
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
		try{
			if (file_exists($classRealPath) && !array_key_exists($className, self::$_c)) {
				self::$_c[$className] = $classRealPath;
				require($classRealPath);
			}else{
				throw new Exception("file {$classRealPath} not found");	
			}
		} catch (Exception $e) { var_dump ($e);}
	}

	/**
	 * 创建web应用实例
	 * 
	 * @param  string $config config文件的详细路径
	 * 
	 * @return object         创建的web应用
	 */
	public static function createWebApplication($config = NULL)
	{
		try {
			if (self::$_w == NULL) {
				self::$_w = new webHttpapplication($config);
			}
		} catch (Exception $e) {
			echo $e->xdebug_message;
		}
		
		return self::$_w;
	}

	/**
     * 通过命名空间前缀获取真实路径
     * 
     * @param string $clsFlags 命名空间前缀
     * 
     * @return string
     */
	public static function getPreFix($classFlag)
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
		}
		return $preFix . DS;
	}

	public static function debug()
	{
		$args = func_get_args();
		ChromePhp::log($args);
	}
}

// 注册函数autoload
spl_autoload_register(array('LAC\LAC', 'autoload'));
