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

    public function __construct( Context $database, SensorsManager $sensorsManager)
    {
        $this->database = $database;
        $this->sensorsManager = $sensorsManager;

    }

    public function zeroOut($number)
    {
        if($number[0] == 0)
            return $number[1];
        else
            return $number;
    }

    // -5- -> -05-
    // -15-

    public function zeroAdd($number)
    {
        if($number<=9)
        {
//            $out[0] = 0;
//            $out[1] = $number;
//            $number[1] = $number[0];
//            $number[0] = 0;
            return intval('0'.$number);
        }
        else
            return $number;
    }

    public function sensorChartData($rawData, $type, $interval, $state)
    {
        $chartData = array();


        switch($type)
        {
//            case 'x':
//                foreach($rawData as $data)
//                {
//                    if($data->state == $state)
//                    {
//                        $time = $data->time;
//                        $hour = $this->zeroOut($time->format('H'));
////
//
//                        if(!isset($chartData[$hour][0]))
//                            $chartData[$hour][0] = 0;
//                        $chartData[$hour][0] += 1;
//
//                        if(!isset($chartData[$hour][1]))
//                        {
//                            $chartData[$hour][1] = $time->format('Y')."-".$time->format('m')."-".$time->format('d')."T".$time->format("H").":00";
//                        }
//                    }
//                }
//                break;

            case 'minute':
                foreach($rawData as $data)
                {
                    if($data->state == $state)
                    {
                        $time = $data->time;
                        $hour = $this->zeroOut($time->format('H'));
                        $minute = $this->zeroOut($time->format('i'));
                        $minute = intval($minute/$interval);
                        $minute *= $interval;

                        if(!isset($chartData[$hour.$minute][0]))
                            $chartData[$hour.$minute][0] = 0;
                        $chartData[$hour.$minute][0] += 1;

                        if(!isset($chartData[$hour.$minute][1]))
                        {
                            $chartData[$hour.$minute][1] = $time->format('Y')."-".$time->format('m')."-".$time->format('d')."T".$time->format("H").":".$this->zeroAdd($minute);
                        }
                        echo"";
                    }
                }
                break;

            case 'hour':
                foreach($rawData as $data)
                {
                    if($data->state == $state)
                    {
                        $time = $data->time;
                        $day = $this->zeroOut($time->format('d'));
                        $hour = $this->zeroOut($time->format('H'));
                        $hour = intval($hour/$interval);
                        $hour *= $interval;

                        if(!isset($chartData[$day.$hour][0]))
                            $chartData[$day.$hour][0] = 0;
                        $chartData[$day.$hour][0] += 1;

                        if(!isset($chartData[$day.$hour][1]))
                        {
                            $chartData[$day.$hour][1] = $time->format('Y')."-".$time->format('m')."-".$time->format('d')."T".$this->zeroAdd($hour).":00";
                        }
                    }
                }
                break;

//            case 'day':
//                foreach($rawData as $data)
//                {
//                    if($data->state == $state)
//                    {
//                        $time = $data->time;
//                        $month = $this->zeroOut($time->format('d'));
//                        $day = $this->zeroOut($time->format('H'));
//                        $day = intval($day/$interval);
//                        $day *= $interval;
//
//                        if(!isset($chartData[$month.$day][0]))
//                            $chartData[$month.$day][0] = 0;
//                        $chartData[$month.$day][0] += 1;
//
//                        if(!isset($chartData[$month.$day][1]))
//                        {
//                            $chartData[$month.$day][1] = $time->format('Y')."-".$time->format('m')."-".$this->zeroAdd($day)."T12:00";
//                        }
//                    }
//                }
//                break;



        }
        return $chartData;
    }
}


