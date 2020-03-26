<?php

namespace App\Model;

use Nette;

class ThisSensorManager
{
	use Nette\SmartObject;

    private $database;
    
    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

}
