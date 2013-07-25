<?php
namespace APP\controller;

use \APP\model\Test;
use \LAC\core\BaseController;
use \LAC\LAC;

class Welcome extends BaseController
{
	public function indexAction()
	{
		$model = new Test();
		$a = $model->update(array('name' => 'waiitt', 'password' => 'aaaaa', 'age' => 14), "id = 4");
		echo $a;
	}

	public function addAction()
	{
		$this->assign("id", $_GET['id']);
		$this->assign("name", $_GET['name']);
		$this->render('add');
	}
}