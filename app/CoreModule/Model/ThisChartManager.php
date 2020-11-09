<?php

namespace App\CoreModule\Model;

use App\TimeManagers\TimeBox;
use App\Utils\NumbersPretty;
use Nette;
use Nette\Database\Context;
use App\CoreModule\Model\ThisSensorManager;
use DateInterval;
use DateTimeZone;
use Nette\Utils\DateTime;
//use DateTime;
use DateTimeImmutable;
use Nette\Database\UniqueConstraintViolationException;
use App\Utils\Pretty;

/**
 * @brief Manage chart data
 */
class ThisChartManager
{
    use Nette\SmartObject;




    private $database;
    private $thisSensorManager;

    public function __construct( Context $database, ThisSensorManager $thisSensorManager)
    {
        $this->database = $database;
        $this->thisSensorManager = $thisSensorManager;

    }

    /**
     * @brief Remove zero from short number
     * @param $number
     * @return string
     */
    public function zeroOut($number)
    {
        if($number[0] == 0)
            return $number[1];
        else
            return $number;
    }

    /**
     * @brief Add zero before short number
     * @param $number
     * @return string
     */
    public function zeroAdd($number)
    {
        if($number<=9)
        {
            return '0'.$number;
        }
        else
            return $number;
    }

    private function chartDataOutputRepairer($chartData, string $type, int $interval)
    {
        switch($type)
        {
            case "HOUR":

        }
    }

    /**
     * @param        $rawData
     * @param string $type type of interval (hour, day, week, month)
     * @param int    $interval count of events per type (4 times per hour)
     * @param        $stateType
     * @return array
     */
    public function sensorChartDataState($rawData, $from, $to, string $type, int $interval, string $stateType)
    {
        $chartData = array();


        switch($type)
        {
            case 'DAY':

                foreach($rawData as $data)
                {
                    if($data->state == $stateType)                          //If correct state
                    {
                        $time = $data->time;                            //Get time
                        $hour = $this->zeroOut($time->format('H'));     //Filter hour
                        $minute = $this->zeroOut($time->format('i'));   //Filter minute
                        $minute = intval(ceil($minute/$interval));
                        $minute *= $interval;                           //Calculate and ceil minute
                        if($minute==60)                                 //Overfloat hour
                        {
                            $hour++;
                            $minute = 00;
                        }

                        if(!isset($chartData[$hour.$minute][0]))        //Dafault value
                        {
                            $chartData[$hour . $minute][0] = 0;
                        }
                        $chartData[$hour.$minute][0] += 1;              //Increment number

                        if(!isset($chartData[$hour.$minute][1]))        //Setup time
                        {
                            $chartData[$hour.$minute][1] = $time->format('Y')."-".$time->format('m')."-".$time->format('d')."T".$this->zeroAdd($hour).":".$this->zeroAdd($minute);
                        }
                    }
                }
                break;

            case 'day':
                $periode = 60/$interval;

                foreach($rawData as $data)
                {
                    if($data->state == $stateType)
                    {
                        $time = $data->time;
                        $day = $this->zeroOut($time->format('d'));
                        $hour = $this->zeroOut($time->format('H'));

                        $hour = intval(ceil($hour/$periode));
                        $hour *= $periode;

                        if($hour==24)
                        {
                            $day++;
                            $hour = 00;
                            echo"";
                        }

                        if(!isset($chartData[$day.$hour][0]))
                        {
                            $chartData[$day . $hour][0] = 0;
                        }
                        $chartData[$day.$hour][0] += 1;

                        if(!isset($chartData[$day.$hour][1]))
                        {
                            $chartData[$day.$hour][1] = $time->format('Y')."-".$time->format('m')."-".$time->format('d')."T".$this->zeroAdd($hour).":00";
                        }
                        echo"";
                    }
                }
                break;

//            case 'd':
//                foreach($rawData as $data)
//                {
//                    if($data->state == $state)
//                    {
//                        $time = $data->time;
//                        $month = $this->zeroOut($time->format('d'));
//                        $day = $this->zeroOut($time->format('H'));
//                        $day = intval($day/$periode);
//                        $day *= $periode;
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

//	public function prepareThisNumberBox(int $number, string $workShift, DateTime $selectionFrom, DateTime $timeboxTo): NumbersPretty
//	{
//		$selectionTo = new $timeboxTo;
//		$selectionTo->setTime($timeboxTo->format("H")-1, 0);
//		// One hour between times is generated in selection
//		$timeboxFrom = new $timeboxTo;
//		$timeboxFrom->setTime(intval($timeboxTo->format("H")), 0);
//
//		$numberBox = new NumbersPretty();
//		$sensorCount = 0;
//		$addCounter = false;
//
//
//
//		$sensorNumberData = $this->databaseSelectionManager->getSelectionData($number, DatabaseSelectionManager::HOUR,$workShift, $selectionFrom, $timeboxTo);
//		if($sensorNumberData->t_all)
//		{
//			$numberBox->state = true;
//			$addCounter = true;
//			$numberBox->finished += $sensorNumberData->c_FINISHED;
//			$numberBox->stopTime += $sensorNumberData->t_stop;
//			$numberBox->workTime += $sensorNumberData->t_work;
//			$numberBox->allTime += $sensorNumberData->t_all;
//		}
//
//		$sensorEvents = $this->thisSensorManager->getAllEvents($number, $timeboxFrom, $timeboxTo);
//		if($sensorEvents)
//		{
//			$numberBox->state = true;
//			$addCounter = true;
//			$previousEvent = $this->thisSensorManager->getPreviousEvent($number, $sensorEvents);
//
//			$timebox = new TimeBox($sensorEvents, $timeboxFrom, $timeboxTo);
//			$stopTime = $timebox->stopTime($previousEvent);
//			$numberBox->stopTime += $stopTime;
//			$allTime = $timebox->allTime($previousEvent);
//			$numberBox->allTime += $allTime;
//			$numberBox->workTime += $timebox->workTime($allTime, $stopTime);
//
//			$numberBox->finished += $timebox->countEvents(TimeBox::FINISHED);
//		}
//		if($addCounter)
//		{
//			$sensorCount++;
//			$addCounter = false;
//		}
//
//
//		if($numberBox->state)
//		{
//			$numberBox->finishedCountToPairs();
//			$numberBox->stopTimeStr = $this->humanTime($numberBox->stopTime);
//
//			$numberBox->rating = intval(($numberBox->workTime*100)/$numberBox->allTime);
//		}
//		else
//		{
//			$numberBox->stopTimeStr = "0 min";
//		}
//
//		return $numberBox;
//	}
}


