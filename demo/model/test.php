<?php
namespace APP\model;

use \LAC\db\BaseModel;

class Test extends BaseModel
{
	public function tableName()
	{
		return 'test';
	}

<<<<<<< HEAD
	public function testt()
	{

=======
	public function showtable()
	{
		$this->getTabs();
>>>>>>> add model action
	}
}