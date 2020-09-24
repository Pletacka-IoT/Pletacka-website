<?php

declare(strict_types=1);

namespace App\CoreModule\Controls\PletackaChartControl;

use Nette;
use App\Forms\FormFactory;
use Nette\Application\UI\Form;
use Nette\Application\UI\Control;
use App\CoreModule\Model\ThisSensorManager;
use App\CoreModule\Model\ThisChartManager;
use App\CoreModule\Model\WorkShiftManager;
use Jakubandrysek\Chart\Serie\DateSerie;
use Jakubandrysek\Chart\DateChart;
use Jakubandrysek\Chart\Segment\DateSegment;
use DateTimeImmutable;

/**
 * @brief
 */
class PletackaChartControl extends  Control{

//    private $poolId;

    private $thisChartManager;
    private $thisSensorManager;
    private $workShiftManager;
    private $sNumber;
    private $fromA;
    private $fromB;
    private $min;
    private $max;
    private $toA;
    private $toB;
    private $color;


    public function __construct($sNumber, ThisSensorManager $thisSensorManager, ThisChartManager $thisChartManager, WorkShiftManager $workShiftManager)
    {
        $this->sNumber = $sNumber;
        $this->thisSensorManager = $thisSensorManager;
        $this->thisChartManager = $thisChartManager;
        $this->workShiftManager = $workShiftManager;
    }

    /**
     * Set time variables - TEST
     */
    private function setTestTime()
    {
        $this->fromA = date_create("2020-05-05 04:00:00");
        $this->toA = date_create("2020-05-05 14:00:00");
        $this->fromB = date_create("2020-05-05 14:00:00");
        $this->toB = date_create("2020-05-05 23:59:00");
    }

    /**
     * Set time variables
     */
    private function setTime()
    {
        $this->fromA = date_create(date("Y-m-d")." 06:00:00");
        $this->toA = date_create(date("Y-m-d")." 14:00:00");
        $this->fromB = date_create(date("Y-m-d")." 14:00:00");
        $this->toB = date_create(date("Y-m-d")." 23:00:00");
    }

    /**
     * Set format info date for chart
     */
    private  function setInfoDate(/*$type*/)
    {
//        switch($type)
//        {
//            case $
//        }
        $this->template->dateA = date_format($this->fromA, "H:i")." - ".date_format($this->toA, "H:i");
        $this->template->dateB = date_format($this->fromB, "H:i")." - ".date_format($this->toB, "H:i");

    }

    private  function getStates($rawEvents, $type, int $interval, $nameType, $color, string $states)
    {
        $serieType = DateSerie::AREA_SPLINE;

        $dataChart = $this->thisChartManager->sensorChartDataState($rawEvents, $type, $interval, $states);
//        dump($dataChart);

        $dayChart = new DateChart();
        $dayChart->enableTimePrecision(); // Enable time accurate to seconds
//        $dayChart->setMinValue(2);
        $dayChart->setMaxValue(6);

        $serie = new DateSerie($serieType, $nameType, $color);
        $first = true;
        foreach($dataChart as $data)
        {
            if($first)
            {
                $this->min = $this->max = 8;//$data[0];
                $first = false;
            }
            if($data[0] != 0 || $data[1] != 0)
            {
                $serie->addSegment(new DateSegment(new DateTimeImmutable($data[1]), $data[0]));
            }
        }
        $dayChart->addSerie($serie);


        return $dayChart;
    }

    public function renderDay( string $color, string $nameType, string $stateType = null)
    {
        // Set ws time
        $this->setTestTime();
//        $this->setTime();

        $this->setInfoDate();

        $this->template->name = $nameType;

        $rawEventsA = $this->thisSensorManager->getAllEvents($this->sNumber, $this->fromA, $this->toA);
        $rawEventsB = $this->thisSensorManager->getAllEvents($this->sNumber, $this->fromB, $this->toB);


        if(!(empty($rawEventsA) and empty($rawEventsB)))
        {
            $this->template->pletackaChartA = $this->getStates($rawEventsA, "hour", 15, $nameType, $color, $stateType);
            $this->template->pletackaChartB = $this->getStates($rawEventsB, "hour", 15, $nameType, $color, $stateType);

            // Render
            $this->template->render(__DIR__ . '/PletackaChartControl.latte');

        }
    }


//    public function renderDay( string $nameType, string $color, string $stateType = null)
//    {
//        $this->template->workShift = $workShift = $this->workShiftManager->getWeekWS();
//
//
//        $this->setTestWS(0);
////        $this->setWS($wShift);
//
//        $this->setDate();
//
//
//        $this->template->name = $nameType;
//
//        $type = DateSerie::AREA_SPLINE;
//
//
//        $this->template->rawEvents = $rawEvents = $this->thisSensorManager->getAllEvents($this->sNumber, $this->from, $this->to);
//
//        if(!empty($rawEvents))
//        {
//            $dayChart = $this->getStates($rawEvents, "m", 15, $nameType, $color, $stateType);
//
//            // Send chart to latte
//            $this->template->pletackaChartA = $dayChart;
//
//
//            // Render
//            $this->template->render(__DIR__ . '/PletackaChartControl.latte');
//
//        }
//    }

    public function renderSet($color)
    {
        $this->color = $color;
    }

    public function renderGet()
    {
        echo($this->color);
    }

    public function render(int $sNumber, int $shift, string $name, string $type, $stateType = null)
    {

//        if($shift == 0)
//        {
//            $from = date("Y-m-d 04:00:00");
//            $to = date("Y-m-d 14:00:00");
//        }
//        else
//        {
//            $from = date("Y-m-d 14:00:00");
//            $to = date("Y-m-d 23:59:00");
//        }


        if($shift == 0)
        {
            $this->from = date("2020-05-05 04:00:00");
            $this->to = date("2020-05-05 14:00:00");
        }
        else
        {
            $this->from = date("2020-05-05 14:00:00");
            $this->to = date("2020-05-05 23:59:00");
        }

//        $from = "2020-05-05 04:00:00";
//        $to = "2020-05-05 14:00:00";

        $this->template->date = "15 - 16 H";
        $this->template->name = $name;

        $type = DateSerie::AREA_SPLINE;


        $this->template->rawEvents = $rawEvents = $this->thisSensorManager->getAllEvents($sNumber, $from, $to);

        if(!empty($rawEvents))
        {
//            $this->template->data = true;

            $interval = 15;

            ($dataChartF = $this->thisChartManager->sensorChartDataState($rawEvents, 'm', $interval, 'FINISHED'));
    //        dump($dataChartF);

            ($dataChartS = $this->thisChartManager->sensorChartDataState($rawEvents, 'm', $interval, 'STOP'));

            $dayChart = new DateChart();
            $dayChart->enableTimePrecision(); // Enable time accurate to seconds

            $serie = new DateSerie($type, 'Upleteno - kusů', 'green');
            foreach($dataChartF as $data)
            {
                if($data[0] != 0 || $data[1] != 0)
                {
                    $serie->addSegment(new DateSegment(new DateTimeImmutable($data[1]), $data[0]));
                }
            }
            $dayChart->addSerie($serie);

            $serie = new DateSerie($type, 'Zastaveno - počet', 'red');
            foreach($dataChartS as $data)
            {
                $serie->addSegment(new DateSegment(new DateTimeImmutable($data[1]), $data[0]));
            }
            $dayChart->addSerie($serie);

            // Send chart to latte
            $this->template->pletackaChart = $dayChart;

            // Render
            $this->template->render(__DIR__ . '/PletackaChartControl.latte');

        }






    }

    public function handleClick()
    {
        echo "OK";
    }

}