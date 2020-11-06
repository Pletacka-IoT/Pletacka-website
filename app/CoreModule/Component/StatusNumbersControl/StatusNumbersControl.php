<?php

declare(strict_types=1);

namespace App\CoreModule\Component\StatusNumbersControl;

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
class StatusNumbersControl extends  Control{


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



    public function prepareNumberBox(array $allSenNumbers, string $workShift, DateTime $selectionFrom, DateTime $timeboxTo): NumbersPretty
    {
		$selectionTo = new DateTime();
	    $selectionTo->setTime($selectionTo->format("H")-1, 0);
	    // One hour between times is generated in selection
	    $timeboxFrom = new DateTime();
	    $timeboxFrom->setTime(intval($timeboxFrom->format("H")), 0);
    	$numberBox = new NumbersPretty();
		$sensorCount = 0;

    	foreach ($allSenNumbers as $sensorNumber)
		{
			$sensorNumberData = $this->databaseSelectionManager->getSelectionData($sensorNumber, DatabaseSelectionManager::HOUR,$workShift, $selectionFrom, $timeboxTo);
			if($sensorNumberData->t_all)
			{
				$numberBox->finished += $sensorNumberData->c_FINISHED;
				$numberBox->stopTime += $sensorNumberData->t_stop;
				$numberBox->workTime += $sensorNumberData->t_work;
				$numberBox->allTime += $sensorNumberData->t_all;


				$sensorEvents = $this->thisSensorManager->getAllEvents($sensorNumber, $timeboxFrom, $timeboxTo);
				$lastEvent = $this->thisSensorManager->getPreviousEvent($sensorNumber, $sensorEvents);
				$timebox = new TimeBox($sensorEvents, $timeboxFrom, $timeboxTo);
				$sensorCount++;
			}
		}
		$numberBox->finishedCountToPairs();
    	$numberBox->divideTimeVariablesByCount($sensorCount);

    	$numberBox->stopTimeStr = $this->humanTime($numberBox->stopTime);

    	$numberBox->rating = intval(($numberBox->workTime*100)/$numberBox->allTime);

    	return $numberBox;
    }

    public function render(array $rooms, string $workShift)
    {
	    if(date("H")<14)
	    {
		    $from = new DateTime(date("Y-m-d 0:00:00"));
			$to = new DateTime(date("Y-m-d 14:00:00"));
	    }
	    else
	    {
		    $from = new DateTime(date("Y-m-d 14:00:00"));
		    $to = new DateTime(date("Y-m-d 23:59:59"));
	    }
	    $now = new DateTime();

	    $allSensorNumbers = $this->multiSensorsManager->getAllSensorsName();
	    $numbersAllUsed = array();

//        $from="2020-04-24 08:00:00"; //For testing
//        $to="2020-04-24 23:00:00";
//        $now = new DateTime("2020-04-24 22:30:00");

	    foreach ($rooms as $room) {
		    foreach($room as $roomRow)
		    {
//			    $bubblesRow = array();
			    foreach($roomRow as $roomSensorNumber)
			    {

				    if(array_key_exists($roomSensorNumber, $allSensorNumbers))
				    {
					    array_push($numbersAllUsed, $roomSensorNumber);
				    }
			    }

		    }
	    }

	    $numbersBox = $this->prepareNumberBox($numbersAllUsed, $workShift, $from, $to);
	    $this->template->numberBox = $numbersBox;
    	$this->template->render(__DIR__ . '/StatusNumbersControl.latte');
    }

    public function handleClick()
    {
        echo "OK";
    }

}