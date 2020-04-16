<?php

namespace App\CoreModule\Model;

use Nette;
use Nette\Database\Context;

class ThisSensorManager
{
    use Nette\SmartObject;

    protected $database;

	public function __construct(Context $database)
	{
		$this->database = $database;
    }
    
}