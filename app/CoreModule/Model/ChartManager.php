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
     * @param        $type
     * @param        $interval
     * @param string $from
     * @param string $to
     * @return array
     * @throws \Exception
     */
    public function sensorsChartData($type, $interval, $from="2000-01-01 00:00:00" , $to="2100-01-01 00:00:00")
    {

        $first = true;

        $chartData = array();
        $chartDataAvg = array();

        $sensorsName = $this->multiSensorsManager->getAllSensorsName();
        dump($allSensors = $this->multiSensorsManager->getAllSensorsEvents($sensorsName, $from, $to));

        foreach($allSensors as $name => $data)
        {
            if(!empty($data))
            {
                $sensor = new TimeBox($data);   //Create object TimeBox

                if($first)
                {
                    $chartData += array("SENSORS" => 1);
                    $chartData += array("ALL_EVENTS" => $sensor->countEvents());
                    $chartData += array(TimeBox::FINISHED => $sensor->countEvents(TimeBox::FINISHED) );
                    $chartData += array(TimeBox::STOP => $sensor->countEvents(TimeBox::STOP));
                    $chartData += array(TimeBox::REWORK => $sensor->countEvents(TimeBox::REWORK));
                    $chartData += array(TimeBox::ON => $sensor->countEvents(TimeBox::ON));
                    $chartData += array(TimeBox::OFF => $sensor->countEvents(TimeBox::OFF));
                    $chartData += array("ALL_TIME" => $sensor->allTime()[1]);
                    $chartData += array("STOP_TIME" => $sensor->stopTime()[1]);
                    $chartData += array("WORK_TIME" => $sensor->workTime()[1]);
                    $chartData += array("AVG_STOP_TIME" => $sensor->avgStopTime()[1]);
                    $chartData += array("AVG_WORK_TIME" => $sensor->avgWorkTime()[1]);
                    $first = false;
                }
                else
                {
                    $chartData["SENSORS"] += 1;
                    $chartData["ALL_EVENTS"] += $sensor->countEvents();
                    $chartData["ALL_EVENTS"] += $sensor->countEvents();
                    $chartData[TimeBox::FINISHED] += $sensor->countEvents(TimeBox::FINISHED);
                    $chartData[TimeBox::STOP] += $sensor->countEvents(TimeBox::STOP);
                    $chartData[TimeBox::REWORK] += $sensor->countEvents(TimeBox::REWORK);
                    $chartData[TimeBox::ON] += $sensor->countEvents(TimeBox::ON);
                    $chartData[TimeBox::OFF] += $sensor->countEvents(TimeBox::OFF);
                    $chartData["ALL_TIME"] += $sensor->allTime()[1];
                    $chartData["STOP_TIME"] += $sensor->stopTime()[1];
                    $chartData["WORK_TIME"] += $sensor->workTime()[1];
                    $chartData["AVG_STOP_TIME"] += $sensor->avgStopTime()[1];
                    $chartData["AVG_WORK_TIME"] += $sensor->avgWorkTime()[1];
                }



//                $chartData += array("" => $sensor->);

                echo("");
//                break;
            }
        }
        return $chartData;
    }


    /**
     * @param $chartData
     * @return mixed
     */
    public function sensorsChartDataAvg($chartData)
    {
        $sensors = $chartData["SENSORS"];
        $chartData["ALL_EVENTS"] /= $sensors;
        $chartData["ALL_EVENTS"] /= $sensors;
        $chartData[TimeBox::FINISHED]  /= $sensors;
        $chartData[TimeBox::STOP]  /= $sensors;
        $chartData[TimeBox::REWORK]  /= $sensors;
        $chartData[TimeBox::ON]  /= $sensors;
        $chartData[TimeBox::OFF]  /= $sensors;
        $chartData["ALL_TIME"]  /= $sensors;
        $chartData["STOP_TIME"]  /= $sensors;
        $chartData["WORK_TIME"]  /= $sensors;
        $chartData["AVG_STOP_TIME"]  /= $sensors;
        $chartData["AVG_WORK_TIME"] /= $sensors;

        return $chartData;
    }


     public function sensorsChartHomepage()
    {

        $first = true;
        $chartData = array();

        if(date("H")<14)
        {
            $from = date("Y-m-d 04:00:00");
            $to = date("Y-m-d 14:00:00");
        }
        else
        {
            $from = date("Y-m-d 14:00:00");
            $to = date("Y-m-d 23:59:00");
        }




        $from="2020-05-05 00:00:00"; //For testing
        $to="2020-05-05 10:00:00";

        $sensorsName = $this->multiSensorsManager->getAllSensorsName();
        dump($allSensors = $this->multiSensorsManager->getAllSensorsEvents($sensorsName, $from, $to));

        foreach($allSensors as $name => $data)
        {
            if(!empty($data))
            {
                $sensor = new TimeBox($data);   //Create object TimeBox

                if($first)
                {
                    $chartData += array("SENSORS" => 1);
                    $chartData += array("ALL_EVENTS" => $sensor->countEvents());
                    $chartData += array(TimeBox::FINISHED => $sensor->countEvents(TimeBox::FINISHED) );
                    $chartData += array(TimeBox::STOP => $sensor->countEvents(TimeBox::STOP));
                    $chartData += array(TimeBox::REWORK => $sensor->countEvents(TimeBox::REWORK));
                    $chartData += array(TimeBox::ON => $sensor->countEvents(TimeBox::ON));
                    $chartData += array(TimeBox::OFF => $sensor->countEvents(TimeBox::OFF));
                    $chartData += array("ALL_TIME" => $sensor->allTime()[1]);
                    $chartData += array("STOP_TIME" => $sensor->stopTime()[1]);
                    $chartData += array("WORK_TIME" => $sensor->workTime()[1]);
                    $chartData += array("AVG_STOP_TIME" => $sensor->avgStopTime()[1]);
                    $chartData += array("AVG_WORK_TIME" => $sensor->avgWorkTime()[1]);
                    $first = false;
                }
                else
                {
                    $chartData["SENSORS"] += 1;
                    $chartData["ALL_EVENTS"] += $sensor->countEvents();
                    $chartData["ALL_EVENTS"] += $sensor->countEvents();
                    $chartData[TimeBox::FINISHED] += $sensor->countEvents(TimeBox::FINISHED);
                    $chartData[TimeBox::STOP] += $sensor->countEvents(TimeBox::STOP);
                    $chartData[TimeBox::REWORK] += $sensor->countEvents(TimeBox::REWORK);
                    $chartData[TimeBox::ON] += $sensor->countEvents(TimeBox::ON);
                    $chartData[TimeBox::OFF] += $sensor->countEvents(TimeBox::OFF);
                    $chartData["ALL_TIME"] += $sensor->allTime()[1];
                    $chartData["STOP_TIME"] += $sensor->stopTime()[1];
                    $chartData["WORK_TIME"] += $sensor->workTime()[1];
                    $chartData["AVG_STOP_TIME"] += $sensor->avgStopTime()[1];
                    $chartData["AVG_WORK_TIME"] += $sensor->avgWorkTime()[1];
                }



                echo("");

            }
        }
        return $chartData;
    }

}


