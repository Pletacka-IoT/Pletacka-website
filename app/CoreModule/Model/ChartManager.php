<?php

namespace App\CoreModule\Model;

use Nette;
use Nette\Database\Context;
use App\CoreModule\Model\MultiSensorsManager;
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
    private $multiSensorsManager;

    public function __construct( Context $database, MultiSensorsManager $multiSensorsManager)
    {
        $this->database = $database;
        $this->multiSensorsManager = $multiSensorsManager;

    }

    public function zeroOut($number)
    {
        if($number[0] == 0)
            return $number[1];
        else
            return $number;
    }

    public function zeroAdd($number)
    {
        if($number<=9)
        {
            return '0'.$number;
        }
        else
            return $number;
    }

    public function sensorsChartData( $type, $interval, $state)
    {
        $sensorsName = $this->multiSensorsManager->getAllSensorsName();
        dump($allSensors = $this->multiSensorsManager->getAllSensorsEvents($sensorsName, "2020-05-05 00:00:00", "2020-05-05 14:00:00"));

        foreach($allSensors as $name => $data)
        {
            dump($name, $data);
        }
    }
}


