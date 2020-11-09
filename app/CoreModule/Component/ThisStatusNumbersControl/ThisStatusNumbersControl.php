<?php

declare(strict_types=1);

namespace App\CoreModule\Component\ThisStatusNumbersControl;

use App\CoreModule\Model\DatabaseSelectionManager;
use App\CoreModule\Model\MultiSensorsManager;
use App\TimeManagers\TimeBox;
use App\Utils\BubblesPretty;
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
		    return $this->timeRemoveFirstNull(gmdate("h", $timeSeconds))." hod";
	    }
    	else
	    {
		    return $this->timeRemoveFirstNull(gmdate("i", $timeSeconds))." mim";
	    }
    }

    public function getCountFinishedTodayWS(int $number, DateTime $from, string $state)
    {
    	return $this->database->table("A".$number)->where("time>? AND state = ?", $from, $state)->count();
    }

    public function getClassName(string $sting): string
    {
    	return "bubble-".strtolower($sting);
    }



    public function prepareThisNumberBox(int $number, string $workShift, DateTime $selectionFrom, DateTime $timeboxTo): NumbersPretty
    {
	    $selectionTo = new $timeboxTo;
	    $selectionTo->setTime($timeboxTo->format("H")-1, 0);
	    // One hour between times is generated in selection
	    $timeboxFrom = new $timeboxTo;
	    $timeboxFrom->setTime(intval($timeboxTo->format("H")), 0);

	    $numberBox = new NumbersPretty();
	    $sensorCount = 0;
	    $addCounter = false;



		$sensorNumberData = $this->databaseSelectionManager->getSelectionData($number, DatabaseSelectionManager::HOUR,$workShift, $selectionFrom, $timeboxTo);
		if($sensorNumberData->t_all)
		{
			$numberBox->state = true;
			$addCounter = true;
			$numberBox->finished += $sensorNumberData->c_FINISHED;
			$numberBox->stopTime += $sensorNumberData->t_stop;
			$numberBox->workTime += $sensorNumberData->t_work;
			$numberBox->allTime += $sensorNumberData->t_all;
		}

		$sensorEvents = $this->thisSensorManager->getAllEvents($number, $timeboxFrom, $timeboxTo);
		if($sensorEvents)
		{
			$numberBox->state = true;
			$addCounter = true;
			$previousEvent = $this->thisSensorManager->getPreviousEvent($number, $sensorEvents);

			$timebox = new TimeBox($sensorEvents, $timeboxFrom, $timeboxTo);
			$stopTime = $timebox->stopTime($previousEvent);
			$numberBox->stopTime += $stopTime;
			$allTime = $timebox->allTime($previousEvent);
			$numberBox->allTime += $allTime;
			$numberBox->workTime += $timebox->workTime($allTime, $stopTime);

			$numberBox->finished += $timebox->countEvents(TimeBox::FINISHED);
		}
		if($addCounter)
		{
			$sensorCount++;
			$addCounter = false;
		}


	    if($numberBox->state)
	    {
	    	$numberBox->finishedCountToPairs();
		    $numberBox->stopTimeStr = $this->humanTime($numberBox->stopTime);

		    $numberBox->rating = intval(($numberBox->workTime*100)/$numberBox->allTime);
	    }
    	else
	    {
	    	$numberBox->stopTimeStr = "0 min";
	    }

    	return $numberBox;
    }

    public function render(int $number, DateTime $from, DateTime $to)
    {
		$wsAll = array("Cahovi", "Vaňkovi");
		$thisNumberBox = array();

		foreach ($wsAll as $ws)
		{
			$thisNumberBoxWs = $this->prepareThisNumberBox($number, $ws, $from, $to);
			 array_push($thisNumberBox, $thisNumberBoxWs);

		}

	    $this->template->thisNumberBox = $thisNumberBox;
    	$this->template->render(__DIR__ . '/ThisStatusNumbersControl.latte');
    }

    public function handleClick()
    {
        echo "OK";
    }

}