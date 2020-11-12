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
    	if($text[0] == 0)
	    {
	    	return $text[1];
	    }
    	else
	    {
	    	return $text;
	    }
    }

    public function humanTime(int $timeSeconds)
    {
    	if($timeSeconds>3600)
	    {
		    return $this->timeRemoveFirstNull(gmdate("h", $timeSeconds))." hod ".$this->timeRemoveFirstNull(gmdate("i", $timeSeconds))." mim";
	    }
    	else
	    {
		    return $this->timeRemoveFirstNull(gmdate("i", $timeSeconds))." mim";
	    }
    }

    public function humanTimeShort(int $timeSeconds)
    {
    	if($timeSeconds>3600)
	    {
		    return $this->timeRemoveFirstNull(gmdate("h", $timeSeconds))." h ".$this->timeRemoveFirstNull(gmdate("i", $timeSeconds))." m";
	    }
    	else
	    {
		    return $this->timeRemoveFirstNull(gmdate("i", $timeSeconds))." m";
	    }
    }


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

			    $numberBox->rating = intval(($numberBox->workTime*100)/$numberBox->allTime);
		    }
		    else
		    {
			    $numberBox->stopTimeStr = "0 min";
	        }
        return $numberBox;

    }

    public function render(int $number, string $workShift, DateTime $from, DateTime $to)
    {
//		$wsAll = array("Cahovi", "Vaňkovi");
//		$thisNumberBox = array();
//
//		foreach ($wsAll as $ws)
//		{
//			$thisNumberBoxWs = $this->prepareThisNumberBox($number, $ws, $from, $to);
//			 array_push($thisNumberBox, $thisNumberBoxWs);
//
//		}


	    $thisNumberBox = $this->prepareThisNumberBox($number, $workShift, $from, $to);


	    $this->template->thisNumberBox = $thisNumberBox;
		dump($thisNumberBox);
    	$this->template->render(__DIR__ . '/ThisStatusNumbersControl.latte');
    }

    public function renderA(int $number, DateTime $from, DateTime $to)
    {
	    $thisNumberBox = $this->prepareThisNumberBox($number, "Cahovi", $from, $to);
	    $this->template->thisNumberBox = $thisNumberBox;
	    dump($thisNumberBox);
	    $this->template->render(__DIR__ . '/ThisStatusNumbersControl.latte');
    }

	public function renderB(int $number, DateTime $from, DateTime $to)
	{
		$thisNumberBox = $this->prepareThisNumberBox($number, "Vaňkovi", $from, $to);
		$this->template->thisNumberBox = $thisNumberBox;
		dump($thisNumberBox);
		$this->template->render(__DIR__ . '/ThisStatusNumbersControl.latte');
	}

    public function handleClick()
    {
        echo "OK";
    }

}