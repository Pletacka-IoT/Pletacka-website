<?php

namespace App\CoreModule\Model;

use Nette;
use Nette\Database\Context;

class DatabaseManager
{
    use Nette\SmartObject;

    protected $database;

	public function __construct(Context $database)
	{
		$this->database = $database;
    }
    
}