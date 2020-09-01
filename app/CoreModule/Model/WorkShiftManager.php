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
        if($ws)
        {
            return array($ws->wsA, $ws->wsB);
        }
        else
        {
            return null;
        }

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

    public function updateWS($year, $week, $wsFirst, $wsSecond)
    {
        return $this->database->table("workShift")
            ->where("year = ? AND week = ?", $year, $week)
            ->update([
            'wsA' => $wsFirst,
            'wsB' => $wsSecond
        ]);
    }

    public function setYear($year, $wsFirst, $wsSecond)
    {
        for($i = 1; $i<=52; $i++)
        {
            if(!$this->getWS($year, $i))
            {
                if($i % 2 == 1) //Odd (lichy)
                {
                    $this->setWS($year, $i, $wsFirst, $wsSecond);
                }else
                {
                    $this->setWS($year, $i, $wsSecond, $wsFirst);
                }
            }
            else
            {

                if($i % 2 == 1) //Odd (lichy)
                {
                    $this->updateWS($year, $i, $wsFirst, $wsSecond);
                }else
                {
                    $this->updateWS($year, $i, $wsSecond, $wsFirst);
                }

            }

        }
        return "Nastaveno";
    }

    public function getActualWS()
    {
        $act = $this->getWS(date("Y"), date("W"));
        if(date("H")<14)
        {
            return $act[0];
        }
        else
        {
            return $act[1];
        }
    }

    public function getWeekWS()
    {
        $act = $this->getWS(date("Y"), date("W"));
        if($act)
        {
            return array($act[0], $act[1]);
        }
        else
        {
            return null;
        }
    }








}


