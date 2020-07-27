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

    public function sensorChartData($rawData, $type, $state)
    {
        $chartData = array();

        for($i = 0; $i<24; $i++)
        {
            $chartData[$i] = array(0 , '');
        }

        switch($type)
        {
            case 'day':
                foreach($rawData as $data)
                {
                    if($data->state == $state)
                    {
                        $time = $data->time;
                        $hour = $this->zeroOut($time->format('H'));
    //                    print($hour);

                        $chartData[$hour][0] += 1;
                        $chartData[$hour][1] = $time->format('Y')."-".$time->format('m')."-".$time->format('d')."T".$time->format("H").":00";
                    }


                }



        }
        return $chartData;
    }
}


