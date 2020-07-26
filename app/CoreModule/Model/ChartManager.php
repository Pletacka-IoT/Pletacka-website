<?php

namespace App\CoreModule\Model;

use Nette;
use Nette\Database\Context;
use App\CoreModule\Model\SensorsManager;
use DateInterval;
use DateTimeZone;
use Nette\Utils\DateTime;
//use DateTime;
use DateTimeImmutable;
use Nette\Database\UniqueConstraintViolationException;
use App\Utils\Pretty;

class ChartManager
{
    use Nette\SmartObject;




    private $database;
    private $defaultMsgLanguage;
    private $defaultAPILanguage;
    private $sensorsManager;

    public function __construct($defaultMsgLanguage,$defaultAPILanguage, Context $database, SensorsManager $sensorsManager)
    {
        $this->database = $database;
        $this->defaultMsgLanguage = $defaultMsgLanguage;
        $this->defaultAPILanguage = $defaultAPILanguage;
        $this->sensorsManager = $sensorsManager;

    }

    public function x()
    {

    }
}


