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



class ChartManager
{
    use Nette\SmartObject;




    private $database;
    private $defaultMsgLanguage;
    private $defaultAPILanguage;
    private $multiSensorsManager;
    private $thisSensorManager;

    public function __construct( Context $database, MultiSensorsManager $multiSensorsManager, ThisSensorManager $thisSensorManager)
    {
        $this->database = $database;
        $this->multiSensorsManager = $multiSensorsManager;
        $this->thisSensorManager = $thisSensorManager;
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


    /**
     *
     * @param $type
     * @param $interval
     * @param $state
     * @return array
     */
    public function sensorsChartData($type, $interval, $state)
    {
        $countSensors = 0;

        $chartData = array();

        $sensorsName = $this->multiSensorsManager->getAllSensorsName();
        dump($allSensors = $this->multiSensorsManager->getAllSensorsEvents($sensorsName, "2020-05-05 00:00:00", "2020-05-05 14:00:00"));

        foreach($allSensors as $name => $data)
        {
            if(!empty($data))
            {
                $countSensors++;    //Count of sensors
                $sensor = new TimeBox($data);   //Create object TimeBox

                $chartData += array("SENSORS" => $countSensors);
                $chartData += array("ALL_EVENTS" => $sensor->countEvents());
                $chartData += array(TimeBox::FINISHED => $sensor->countEvents(TimeBox::FINISHED));
                $chartData += array(TimeBox::STOP => $sensor->countEvents(TimeBox::STOP));
                $chartData += array(TimeBox::REWORK => $sensor->countEvents(TimeBox::REWORK));
                $chartData += array(TimeBox::ON => $sensor->countEvents(TimeBox::ON));                          //PLUS
                $chartData += array(TimeBox::OFF => $sensor->countEvents(TimeBox::OFF));
                $chartData += array("ALL_TIME" => $sensor->allTime()[1]);
                $chartData += array("STOP_TIME" => $sensor->stopTime()[1]);
                $chartData += array("WORK_TIME" => $sensor->workTime()[1]);
                $chartData += array("AVG_STOP_TIME" => $sensor->avgStopTime()[1]);
                $chartData += array("AVG_WORK_TIME" => $sensor->avgWorkTime()[1]);
//                $chartData += array("" => $sensor->);

                echo("");
            }
        }
        return $chartData;
    }
}


