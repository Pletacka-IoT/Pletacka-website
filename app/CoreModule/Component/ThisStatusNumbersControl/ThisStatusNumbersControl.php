<?php

declare(strict_types=1);

namespace App\CoreModule\Component\ThisStatusNumbersControl;

use App\CoreModule\Model\DatabaseSelectionManager;
use App\CoreModule\Model\MultiSensorsManager;
use App\TimeManagers\TimeBox;
use App\Utils\BubblesPretty;
use App\Utils\DatabaseDataExtractorPretty;
use App\Utils\DatabaseSelectionPretty;
use App\Utils\NumbersPretty;
use App\Utils\Pretty;
use DateInterval;
use Hoa\Ustring\Bin\Fromcode;
use Nette;
use App\Forms\FormFactory;
use Nette\Application\UI\Form;
use Nette\Database\Context;
use Nette\Application\UI\Control;
use App\CoreModule\Model\ThisSensorManager;
use App\CoreModule\Model\ThisChartManager;
use App\CoreModule\Model\WorkShiftManager;
use Jakubandrysek\Chart\Serie\DateSerie;
use Jakubandrysek\Chart\DateChart;
use Jakubandrysek\Chart\Segment\DateSegment;
use DateTimeImmutable;
use Nette\Utils\DateTime;

/**
 * @brief
 */
class ThisStatusNumbersControl extends  Control{


//    private $poolId;

    private $thisChartManager;
    private $thisSensorManager;
    private $workShiftManager;
	/**
	 * @var MultiSensorsManager
	 */
	private $multiSensorsManager;
	/**
	 * @var Context
	 */
	private $database;
	/**
	 * @var DatabaseSelectionManager
	 */
	private $databaseSelectionManager;


	public function __construct(MultiSensorsManager $multiSensorsManager,
	                            ThisSensorManager $thisSensorManager,
								Context $database,
								DatabaseSelectionManager $databaseSelectionManager)
    {
	    $this->multiSensorsManager = $multiSensorsManager;
	    $this->thisSensorManager = $thisSensorManager;
	    $this->database = $database;
	    $this->databaseSelectionManager = $databaseSelectionManager;
    }

    public function timeRemoveFirstNull($text)
    {
    	$text = strval($text);
    	if($text == 0){return 0;}
    	if($text[0] == 0)
	    {
	    	return $text[1];
	    }
    	else
	    {
	    	return $text;
	    }
    }

	function secondsToTime($secs)
	{
		$dt = new DateTime('@' . $secs);
		return array(
//			'months'    => intval($dt->format('z')/30),
//			'days'    => $dt->format('z')%30,
//			'days'    => ,
			'hours'   => $this->timeRemoveFirstNull(intval($dt->format('G')) + $dt->format('z')*24),
			'minutes' => $this->timeRemoveFirstNull($dt->format('i')),
			'seconds' => $this->timeRemoveFirstNull($dt->format('s')));
	}


    public function humanTime(int $timeSeconds)
    {
	    $time = $this->secondsToTime($timeSeconds);
	    if($time["months"]>=1)
	    {
		    return $time["months"]." měs ".$time["days"]." d";
	    }
	    else if($time["days"]>=1)
	    {
		    return $time["days"]." d ".$time["hours"]." hod";
	    }
	    else if($time["hours"]>=1)
	    {
		    return $time["hours"]." hod ".$time["minutes"]." min";
	    }
	    else if($time["minutes"]>=1)
	    {
		    return $time["minutes"]." min ".$time["seconds"]." sec";
	    }
	    else
	    {
	    	return $time["seconds"]." sec";
	    }
    }


    public function humanTimeShort(int $timeSeconds)
    {
	    $time = $this->secondsToTime($timeSeconds);
//	    if($time["months"]>=1)
//	    {
//		    return $time["months"]."M ".$time["days"]."D";
//	    }
//	    else if($time["days"]>=1)
//	    {
//		    return $time["days"]."D ".$time["hours"]."H";
//	    }
	    if($time["hours"]>=1)
	    {
		    return $time["hours"]."H ".$time["minutes"]."M";
	    }
	    else if($time["minutes"]>=1)
	    {
		    return $time["minutes"]."M ".$time["seconds"]."S";
	    }
	    else
	    {
	    	return $time["seconds"]."S";
	    }
    }


//
//    public function humanTimeShort(int $timeSeconds)
//    {
//    	if($timeSeconds>3600)
//	    {
//		    return $this->timeRemoveFirstNull(gmdate("h", $timeSeconds))."h ".$this->timeRemoveFirstNull(gmdate("i", $timeSeconds))."m";
//	    }
//    	else
//	    {
////		    return $this->timeRemoveFirstNull(gmdate("i", $timeSeconds))."m";
//		    $dv = new DateInterval('PT'.$timeSeconds.'S');
//		    return $dv->format("%h:%i:%s");
//	    }
//    }


    public function getClassName(string $sting): string
    {
    	return "bubble-".strtolower($sting);
    }



    public function prepareThisNumberBox(int $number, string $workShift, DateTime $from, DateTime $to): DatabaseDataExtractorPretty
    {
////	    $selectionTo = new DateTime($to);
////	    $selectionTo->setTime($timeboxTo->format("H")-1, 0);
////	    // One hour between times is generated in selection
////	    $timeboxFrom = new $timeboxTo;
////	    $timeboxFrom->setTime(intval($timeboxTo->format("H")), 0);
//
//	    $numberBox = new NumbersPretty();
//
//		$sensorNumberData = $this->databaseSelectionManager->getSelectionData($number, DatabaseSelectionManager::HOUR,$workShift, $from, $to);
//		if($sensorNumberData->allTime)
//		{
//			$numberBox->state = true;
//			$addCounter = true;
//			$numberBox->finishedCount = $sensorNumberData->finishedCount;
//			$numberBox->stopTime = $sensorNumberData->stopTime;
//			$numberBox->workTime = $sensorNumberData->workTime;
//			$numberBox->allTime = $sensorNumberData->allTime;
//
//			$numberBox->finishedCountToPairs();
//			$numberBox->stopTimeStr = $this->humanTime($numberBox->stopTime);
//
//			$numberBox->rating = intval(($numberBox->workTime*100)/$numberBox->allTime);
//		}
//    	else
//	    {
//	    	$numberBox->stopTimeStr = "0 min";
//	    }
//
//    	return $numberBox;

	    $selectionTo = new DateTime();
	    $selectionTo->setTime($selectionTo->format("H")-1, 0);
	    // One hour between times is generated in selection
	    $timeboxFrom = new DateTime();
	    $timeboxFrom->setTime(intval($timeboxFrom->format("H")), 0);
	    $timeboxTo = new DateTime();

	    $numberBox = new DatabaseDataExtractorPretty();
	    $numberBox->workShift = $workShift;
	    $numberBox->from = $from;
	    $numberBox->to = $to;
	    $numberBox->number = $number;
	    $addCounter = false;


		    $sensorNumberData = $this->databaseSelectionManager->getSelectionData($number, DatabaseSelectionManager::HOUR,$workShift, $from, $to);
		    if($sensorNumberData->status)
		    {
			    $numberBox->status = true;
			    $addCounter = true;
			    $numberBox->finishedCount += $sensorNumberData->finishedCount;
			    $numberBox->stopCount += $sensorNumberData->stopCount;
			    $numberBox->stopTime += $sensorNumberData->stopTime;
			    $numberBox->workTime += $sensorNumberData->workTime;
			    $numberBox->allTime += $sensorNumberData->allTime;
		    }

		    $sensorEvents = $this->thisSensorManager->getAllEvents($number, $timeboxFrom, $timeboxTo);
		    if($sensorEvents)
		    {
			    $numberBox->status = true;
			    $addCounter = true;
			    $previousEvent = $this->thisSensorManager->getPreviousEvent($number, $sensorEvents);

			    $timebox = new TimeBox($sensorEvents, $previousEvent, $timeboxFrom, $timeboxTo);
			    $stopTime = $timebox->stopTime();
			    $numberBox->stopTime += $stopTime;
			    $allTime = $timebox->allTime();
			    $numberBox->allTime += $allTime;
			    $numberBox->workTime += $timebox->workTime($allTime, $stopTime);

			    $numberBox->finishedCount += $timebox->countEvents(TimeBox::FINISHED);
			    $numberBox->stopCount += $timebox->countEvents(TimeBox::STOP);
		    }
		    if($addCounter)
		    {
			    $numberBox->finishedCount = intval(ceil($numberBox->finishedCount/2));

			    $numberBox->stopTimeStr = $this->humanTimeShort($numberBox->stopTime);
			    $numberBox->workTimeStr= $this->humanTimeShort($numberBox->workTime);
			    $numberBox->allTimeStr= $this->humanTimeShort($numberBox->allTime);

				$numberBox->workTimeAvg = intval($numberBox->workTime/$numberBox->finishedCount);
			    $numberBox->workTimeAvgStr = $this->humanTimeShort($numberBox->workTimeAvg);

				$numberBox->stopTimeAvg = intval($numberBox->stopTime/$numberBox->stopCount);
			    $numberBox->stopTimeAvgStr = $this->humanTimeShort($numberBox->stopTimeAvg);



			    $numberBox->rating = intval(($numberBox->workTime*100)/$numberBox->allTime);
		    }
		    else
		    {
//			    $numberBox->stopTimeStr = "0 min";
//			    $numberBox->allTimeStr = "0 min";
//			    $numberBox->stopTimeStr = "0 min";
	        }
        return $numberBox;

    }

    public function thisNumberBoxes(int $number, string $workShift)
    {
	    $thisNumberBoxes = array();

    	$fromDay = new DateTime();
	    $fromDay->setTimestamp(strtotime("today"));
	    $toDay = new DateTime();
	    $toDay->setTimestamp(strtotime("tomorrow")-1);
    	$thisNumberBoxes["DAY"] = $this->prepareThisNumberBox($number, $workShift, $fromDay, $toDay);


    	$fromWeek = new DateTime();
    	$fromWeek->setTimestamp(strtotime("today"));
    	$fromWeek->sub(DateInterval::createFromDateString("1 week"));
	    $toWeek = new DateTime();
	    $toWeek->setTimestamp(strtotime("tomorrow")-1);
    	$thisNumberBoxes["WEEK"] = $this->prepareThisNumberBox($number, $workShift, $fromWeek, $toWeek);

    	$fromMonth = new DateTime();
    	$fromMonth->setTimestamp(strtotime("today"));
    	$fromMonth->sub(DateInterval::createFromDateString("1 month"));
	    $toMonth = new DateTime();
	    $toMonth->setTimestamp(strtotime("tomorrow")-1);
    	$thisNumberBoxes["MONTH"] = $this->prepareThisNumberBox($number, $workShift, $fromMonth, $toMonth);
	    return $thisNumberBoxes;
    }

    public function render(int $number, string $workShift)
    {
	    $thisNumberBox = $this->thisNumberBoxes($number, $workShift);
	    $this->template->thisNumberBox = $thisNumberBox;

	    $this->template->render(__DIR__ . '/ThisStatusNumbersControl.latte');
//	    dump($thisNumberBox);
    }

//    public function renderA(int $number)
//    {
//		$this->render($number, "Cahovi");
//    }
//
//	public function renderB(int $number)
//	{
//		$this->render($number, "Vaňkovi");
//	}

}