<?php

namespace App\CoreModule\Model;

use Nette;
use Nette\Database\Context;
use App\CoreModule\Model\MultiSensorsManager;
use App\CoreModule\Model\ThisSensorManager;
use App\CoreModule\Model\RoomManager;
use DateInterval;
use DateTimeZone;
use Nette\Utils\DateTime;
//use DateTime;
use DateTimeImmutable;
use Nette\Database\UniqueConstraintViolationException;
use App\Utils\Pretty;
use App\TimeManagers\TimeBox;


/**
 * @brief Class Chart Manager
 */
class ChartManager
{
    use Nette\SmartObject;




    private $database;
    private $defaultMsgLanguage;
    private $defaultAPILanguage;
    private $multiSensorsManager;
    private $thisSensorManager;
    private $roomManager;

    public function __construct( Context $database, MultiSensorsManager $multiSensorsManager, ThisSensorManager $thisSensorManager, RoomManager $roomManager)
    {
        $this->database = $database;
        $this->multiSensorsManager = $multiSensorsManager;
        $this->thisSensorManager = $thisSensorManager;
        $this->roomManager = $roomManager;
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

        $sensorsNumber = $this->multiSensorsManager->getAllSensorsName();
//        dump($allSensors = $this->multiSensorsManager->getAllSensorsEvents($sensorsNumber, $from, $to));



        foreach($allSensors as $number => $data)
        {
            if(!empty($data["raw"]))
            {
                $sensor = new TimeBox($data["raw"], $data["from"], $data["to"]);//Create object TimeBox

                if($first)
                {
                    $chartData += array("SENSORS" => 1);
                    $chartData += array("ALL_EVENTS" => $sensor->countEvents());
                    $chartData += array(TimeBox::FINISHED => $sensor->countEvents(TimeBox::FINISHED) );
                    $chartData += array(TimeBox::STOP => $sensor->countEvents(TimeBox::STOP));
                    $chartData += array(TimeBox::REWORK => $sensor->countEvents(TimeBox::REWORK));
                    $chartData += array(TimeBox::ON => $sensor->countEvents(TimeBox::ON));
                    $chartData += array(TimeBox::OFF => $sensor->countEvents(TimeBox::OFF));
                    $chartData += array("ALL_TIME" => $sensor->allTime($data["previous"]));
                    $chartData += array("STOP_TIME" => $sensor->stopTime($data["previous"]));
                    $chartData += array("WORK_TIME" => $sensor->workTime($data["previous"]));
                    $chartData += array("AVG_STOP_TIME" => $sensor->avgStopTime($data["previous"]));
                    $chartData += array("AVG_WORK_TIME" => $sensor->avgWorkTime($data["previous"]));
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
                    $chartData["ALL_TIME"] += $sensor->allTime($data["previous"]);
                    $chartData["STOP_TIME"] += $sensor->stopTime($data["previous"]);
                    $chartData["WORK_TIME"] += $sensor->workTime($data["previous"]);
                    $chartData["AVG_STOP_TIME"] += $sensor->avgStopTime($data["previous"]);
                    $chartData["AVG_WORK_TIME"] += $sensor->avgWorkTime($data["previous"]);
                }



//                $chartData += array("" => $sensor->);

                echo("");
//                break;
            }
            $chartData += array("DATA" => false);
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




//        $from="2020-05-05 00:00:00"; //For testing
//        $to="2020-05-05 10:00:00";

        $sensorsName = $this->multiSensorsManager->getAllSensorsName();
        /*dump*/($allSensors = $this->multiSensorsManager->getAllSensorsEvents($sensorsName, $from, $to));

        foreach($allSensors as $number => $data)
        {
            if(!empty($data["raw"]))
            {
                $sensor = new TimeBox($data["raw"], $data["from"], $data["to"]);;   //Create object TimeBox

                if($first)
                {
                    $chartData += array("DATA" => true);
                    $chartData += array("SENSORS" => 1);
                    $chartData += array("ALL_EVENTS" => $sensor->countEvents());
                    $chartData += array(TimeBox::FINISHED => $sensor->countEvents(TimeBox::FINISHED) );
                    $chartData += array(TimeBox::STOP => $sensor->countEvents(TimeBox::STOP));
                    $chartData += array(TimeBox::REWORK => $sensor->countEvents(TimeBox::REWORK));
                    $chartData += array(TimeBox::ON => $sensor->countEvents(TimeBox::ON));
                    $chartData += array(TimeBox::OFF => $sensor->countEvents(TimeBox::OFF));
                    $chartData += array("ALL_TIME" => $sensor->allTime($data["previous"]));
                    $chartData += array("STOP_TIME" => $sensor->stopTime($data["previous"]));
                    $chartData += array("WORK_TIME" => $sensor->workTime($data["previous"]));
                    $chartData += array("AVG_STOP_TIME" => $sensor->avgStopTime($data["previous"]));
                    $chartData += array("AVG_WORK_TIME" => $sensor->avgWorkTime($data["previous"]));
                    $chartData += array("ALL_SENSORS" => $allSensors);

                    $first = false;
                }
                else
                {
                    $chartData["SENSORS"] += 1;
                    $chartData["ALL_EVENTS"] += $sensor->countEvents();
                    $chartData[TimeBox::FINISHED] += $sensor->countEvents(TimeBox::FINISHED);
                    $chartData[TimeBox::STOP] += $sensor->countEvents(TimeBox::STOP);
                    $chartData[TimeBox::REWORK] += $sensor->countEvents(TimeBox::REWORK);
                    $chartData[TimeBox::ON] += $sensor->countEvents(TimeBox::ON);
                    $chartData[TimeBox::OFF] += $sensor->countEvents(TimeBox::OFF);
                    $chartData["ALL_TIME"] += $sensor->allTime($data["previous"]);
                    $chartData["STOP_TIME"] += $sensor->stopTime($data["previous"]);
                    $chartData["WORK_TIME"] += $sensor->workTime($data["previous"]);
                    $chartData["AVG_STOP_TIME"] += $sensor->avgStopTime($data["previous"]);
                    $chartData["AVG_WORK_TIME"] += $sensor->avgWorkTime($data["previous"]);
                }



                echo("");

            }
            else
            {
                $chartData += array("DATA" => false);
            }
        }
        return $chartData;
    }


    public function sensorsChartBubbles($roomSensors)
    {

        $chartData = array();
        $counter = 1;

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



//
//        $from="2020-04-24 08:00:00"; //For testing
//        $to="2020-04-24 23:00:00";

        $from="2020-05-05 04:00:00"; //For testing
        $to="2020-05-05 12:29:00";

        $sensorsName = $this->multiSensorsManager->getAllSensorsName();
        $roomSensorsArray = array();

        $coulmnsCounterLive = 0;
        $coulmnsCounter = 0;

        foreach($roomSensors as $sensorArr)
        {
            foreach($sensorArr as $sensor)
            {
                if(array_key_exists($sensor, $sensorsName))
                {
                    array_push($roomSensorsArray, $sensor);
                }

                $coulmnsCounterLive++;

            }
            if($coulmnsCounterLive>$coulmnsCounter)
                $coulmnsCounter = $coulmnsCounterLive;
            $coulmnsCounterLive = 0;
        }

        /*dump*/($allSensors = $this->multiSensorsManager->getAllSensorsEvents($roomSensorsArray, $from, $to, false));
        echo "";
//        dump($allSensors);

        foreach($allSensors as $number => $data)
        {
            $sensorData = array();

            if(!empty($data["raw"]))
            {


                $sensor = new TimeBox($data["raw"], $data["from"], $data["to"]);   //Create object TimeBox

                //                $sensorData += array("SENSORS" => 1);
                //                $chartData += array("ALL_EVENTS" => $sensor->countEvents());
                $sensorData += array(TimeBox::FINISHED => $sensor->countEvents(TimeBox::FINISHED) );
                //                $chartData += array(TimeBox::STOP => $sensor->countEvents(TimeBox::STOP));
                //                $chartData += array(TimeBox::REWORK => $sensor->countEvents(TimeBox::REWORK));
                //                $chartData += array(TimeBox::ON => $sensor->countEvents(TimeBox::ON));
                //                $chartData += array(TimeBox::OFF => $sensor->countEvents(TimeBox::OFF));
//                                $chartData += array("ALL_TIME" => $sensor->allTime($data["previous"])[1]);
                $sensorData += array("STOP_TIME" => $sensor->stopTime($data["previous"]));
//                                $chartData += array("WORK_TIME" => $sensor->workTime($data["previous"])[1]);
                //                $chartData += array("AVG_STOP_TIME" => $sensor->avgStopTime()[1]);
                //                $chartData += array("AVG_WORK_TIME" => $sensor->avgWorkTime()[1]);

                $sensorData += array("LAST_STATE" => $data["last"]);
//                dump($data[array_key_last($data)]);

                $x = $sensor->allTime($data["previous"]);
                $y = $sensor->workTime($data["previous"]);
                $z = $sensor->stopTime($data["previous"]);
            }
            else
            {
                $sensorData += array(TimeBox::FINISHED => 0);
                $sensorData += array("STOP_TIME" => 0);
                $sensorData += array("LAST_STATE" => "OFF");
            }
            $sensorData += array("COUNTER" => $counter);
            $sensorData += array("COLUMN" => $coulmnsCounter);
            $sensorData += array("VISIBILITY" => "VISIBLY");
            $counter++;

            $chartData += array($number => $sensorData);
        }


        $sortChartData = array();

        $invisibleCounter = -1;


        foreach($roomSensors as $positionArr)
        {
            foreach($positionArr as $position)
            {

                if(array_key_exists($position, $chartData))
                {
                    $sortChartData += array($position => $chartData[$position]);
                }
                else
                {
                    $sortChartData += array($invisibleCounter => array("VISIBILITY" => "HIDDEN"));
                    $sortChartData[$invisibleCounter] += array("COLUMN" => "12");
                    $invisibleCounter--;
                }

//                $sortChartData += array($position => array("COLUMN" => $coulmnsCounter));

            }
        }
        return $sortChartData;
    }



//
//    public function sensorsChartBubblesOld()
//    {
//
//        $chartData = array();
//        $counter = 1;
//
//        if(date("H")<14)
//        {
//            $from = date("Y-m-d 04:00:00");
//            $to = date("Y-m-d 14:00:00");
//        }
//        else
//        {
//            $from = date("Y-m-d 04:00:00");
//            $to = date("Y-m-d 23:59:00");
//        }
//
//
//
//
//        $from="2020-05-05 00:00:00"; //For testing
//        $to="2020-05-05 10:00:00";
//
//        $sensorsName = $this->multiSensorsManager->getAllSensorsName();
//        /*dump*/($allSensors = $this->multiSensorsManager->getAllSensorsEvents($sensorsName, $from, $to));
//
//        foreach($allSensors as $number => $data)
//        {
//            $sensorData = array();
//
//            if(!empty($data))
//            {
//
//
//                $sensor = new TimeBox($data, 0, 24);   //Create object TimeBox
//
////                $sensorData += array("SENSORS" => 1);
////                $chartData += array("ALL_EVENTS" => $sensor->countEvents());
//                $sensorData += array(TimeBox::FINISHED => $sensor->countEvents(TimeBox::FINISHED) );
////                $chartData += array(TimeBox::STOP => $sensor->countEvents(TimeBox::STOP));
////                $chartData += array(TimeBox::REWORK => $sensor->countEvents(TimeBox::REWORK));
////                $chartData += array(TimeBox::ON => $sensor->countEvents(TimeBox::ON));
////                $chartData += array(TimeBox::OFF => $sensor->countEvents(TimeBox::OFF));
////                $chartData += array("ALL_TIME" => $sensor->allTime()[1]);
//                $sensorData += array("STOP_TIME" => $sensor->stopTime());
////                $chartData += array("WORK_TIME" => $sensor->workTime()[1]);
////                $chartData += array("AVG_STOP_TIME" => $sensor->avgStopTime()[1]);
////                $chartData += array("AVG_WORK_TIME" => $sensor->avgWorkTime()[1]);
//                $sensorData += array("LAST_STATE" => $data[array_key_last($data)]->state);
//
//
//
//                echo("");
//
//            }
//            else
//            {
//                $sensorData += array(TimeBox::FINISHED => 0);
//                $sensorData += array("STOP_TIME" => 0);
//                $sensorData += array("LAST_STATE" => "OFF");
//            }
//            $sensorData += array("COUNTER" => $counter);
//            $sensorData += array("VISIBILITY" => "VISIBLY");
//            $counter++;
//
//            $chartData += array($number => $sensorData);
//        }
//
//        $pletarnaBig = $this->roomManager->roomPletarnaBig;
//
//        $sortChartData = array();
//
//        $invisibleCounter = -1;
//
//        foreach($pletarnaBig as $positionArr)
//        {
//            foreach($positionArr as $position)
//            {
//                if(array_key_exists($position, $chartData))
//                {
//                    $sortChartData += array($position => $chartData[$position]);
//                }
//                else
//                {
//                    $sortChartData += array($invisibleCounter => array("VISIBILITY" => "HIDDEN"));
//                    $invisibleCounter--;
//                }
//
//            }
//        }
//
//        return $sortChartData;
//    }

}


