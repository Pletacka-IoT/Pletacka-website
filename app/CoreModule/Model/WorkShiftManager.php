<?php

namespace App\CoreModule\Model;

use Nette;
use Nette\Database\Context;
use App\CoreModule\Model\MultiSensorsManager;
use App\CoreModule\Model\ThisSensorManager;
use DateInterval;
use DateTimeZone;
use Nette\Utils\DateTime;
//use DateTime;
use DateTimeImmutable;
use Nette\Database\UniqueConstraintViolationException;
use App\Utils\Pretty;
use App\TimeManagers\TimeBox;


/**
 * @brief Manage work shifts
 */
class WorkShiftManager
{
    use Nette\SmartObject;


    private $database;

    public function __construct( Context $database)
    {
        $this->database = $database;
    }

    public function getWS($year, $week)
    {
        $ws =  $this->database->table("workShift")->where("year = ? AND week = ?", $year, $week)->fetch();
        return array($ws->wsA, $ws->wsB);
    }

    public function setWS($year, $week, $wsFirst, $wsSecond)
    {
        return $this->database->table("workShift")->insert([
            'year' => $year,
            'week' => $week,
            'wsA' => $wsFirst,
            'wsB' => $wsSecond
        ]);
    }





}


