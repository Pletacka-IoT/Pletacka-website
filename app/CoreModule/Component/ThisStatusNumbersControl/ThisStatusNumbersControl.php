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
use PhpParser\Node\Scalar\String_;

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
								DatabaseSelectionManager $databaseSelectionManager,
								WorkShiftManager $workShiftManager)
    {
	    $this->multiSensorsManager = $multiSensorsManager;
	    $this->thisSensorManager = $thisSensorManager;
	    $this->database = $database;
	    $this->databaseSelectionManager = $databaseSelectionManager;
	    $this->workShiftManager = $workShiftManager;
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



    public function getClassName(string $sting): string
    {
    	return "bubble-".strtolower($sting);
    }

	/**
	 * @param $string
	 * @return string
	 */
	public function clean($string): string
	{
		$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
		$string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

		return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
	}


	/**
	 * @param int $number
	 * @param string $workShift
	 * @param DateTime $from
	 * @param DateTime $to
	 * @param string $dateText
	 * @return DatabaseDataExtractorPretty
	 */
	public function prepareThisNumberBox(int $number, string $workShift, DateTime $from, DateTime $to, string $dateText): DatabaseDataExtractorPretty
    {

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
	    $numberBox->msg = $dateText;
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

	    // TODO add better ws check (13:59->14:05)
	    if($this->workShiftManager->getActualWS() == $workShift)
		    {
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
		    }

		    if($addCounter)
		    {
			    $numberBox->finishedCount = intval(ceil($numberBox->finishedCount/2));

			    $numberBox->stopTimeStr = $this->humanTimeShort($numberBox->stopTime);
			    $numberBox->workTimeStr= $this->humanTimeShort($numberBox->workTime);
			    $numberBox->allTimeStr= $this->humanTimeShort($numberBox->allTime);

				$numberBox->workTimeAvg = intval($this->safeDivision($numberBox->workTime, $numberBox->finishedCount));
			    $numberBox->workTimeAvgStr = $this->humanTimeShort($numberBox->workTimeAvg);

				$numberBox->stopTimeAvg = intval($this->safeDivision($numberBox->stopTime, $numberBox->stopCount));
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


    public function prepareNumberBox(Nette\Database\Table\Selection $sensorNumbers, string $workShift, DateTime $from, DateTime $to, string $dateText): DatabaseDataExtractorPretty
    {
	    $numberBox = new DatabaseDataExtractorPretty();

    	foreach ($sensorNumbers as $sensorNumber) {
		    $sensor = $this->prepareThisNumberBox($sensorNumber->number, $workShift, $from, $to, "");
		    if($sensor->status)
		    {
			    $numberBox->add($sensor);
		    }
    	}

		$numberBox->msg = $dateText;
	    $numberBox->stopTimeStr = $this->humanTimeShort($numberBox->stopTime);
	    $numberBox->workTimeStr= $this->humanTimeShort($numberBox->workTime);
	    $numberBox->allTimeStr= $this->humanTimeShort($numberBox->allTime);

	    $numberBox->workTimeAvg = intval($this->safeDivision($numberBox->workTime, $numberBox->finishedCount));
	    $numberBox->workTimeAvgStr = $this->humanTimeShort($numberBox->workTimeAvg);

	    $numberBox->stopTimeAvg = intval($this->safeDivision($numberBox->stopTime, $numberBox->stopCount));
	    $numberBox->stopTimeAvgStr = $this->humanTimeShort($numberBox->stopTimeAvg);

	    return $numberBox;
    }


	private function safeDivision(int |float $dividend, int | float $divisor)
	{
		if($divisor == 0)
		{
			return 0;
		}
		else
		{
			return $dividend/$divisor;
		}
    }



    public function thisNumberBoxes(int $number, string $workShift)
    {
	    $thisNumberBoxes = array();

    	$fromDay = new DateTime();
	    $fromDay->setTimestamp(strtotime("today"));
	    $toDay = new DateTime();
	    $toDay->setTimestamp(strtotime("tomorrow")-1);
    	$thisNumberBoxes["DAY"] = $this->prepareThisNumberBox($number, $workShift, $fromDay, $toDay, "Dnes");


    	$fromWeek = new DateTime();
    	$fromWeek->setTimestamp(strtotime("today"));
    	$fromWeek->sub(DateInterval::createFromDateString("1 week"));
	    $toWeek = new DateTime();
	    $toWeek->setTimestamp(strtotime("tomorrow")-1);
    	$thisNumberBoxes["WEEK"] = $this->prepareThisNumberBox($number, $workShift, $fromWeek, $toWeek, "Poslední týden");

    	$fromMonth = new DateTime();
    	$fromMonth->setTimestamp(strtotime("today"));
    	$fromMonth->sub(DateInterval::createFromDateString("1 month"));
	    $toMonth = new DateTime();
	    $toMonth->setTimestamp(strtotime("tomorrow")-1);
    	$thisNumberBoxes["MONTH"] = $this->prepareThisNumberBox($number, $workShift, $fromMonth, $toMonth, "Poslední měsíc");
	    return $thisNumberBoxes;
    }


    public function numberBoxes(Nette\Database\Table\Selection | null $sensorNumbers, string $workShift)
    {
	    $thisNumberBoxes = array();

    	$fromDay = new DateTime();
	    $fromDay->setTimestamp(strtotime("today"));
	    $toDay = new DateTime();
	    $toDay->setTimestamp(strtotime("tomorrow")-1);
    	$thisNumberBoxes["DAY"] = $this->prepareNumberBox($sensorNumbers, $workShift, $fromDay, $toDay, "Dnes");


    	$fromWeek = new DateTime();
    	$fromWeek->setTimestamp(strtotime("today"));
    	$fromWeek->sub(DateInterval::createFromDateString("1 week"));
	    $toWeek = new DateTime();
	    $toWeek->setTimestamp(strtotime("tomorrow")-1);
    	$thisNumberBoxes["WEEK"] = $this->prepareNumberBox($sensorNumbers, $workShift, $fromWeek, $toWeek, "Poslední týden");

    	$fromMonth = new DateTime();
    	$fromMonth->setTimestamp(strtotime("today"));
    	$fromMonth->sub(DateInterval::createFromDateString("1 month"));
	    $toMonth = new DateTime();
	    $toMonth->setTimestamp(strtotime("tomorrow")-1);
    	$thisNumberBoxes["MONTH"] = $this->prepareNumberBox($sensorNumbers, $workShift, $fromMonth, $toMonth, "Poslední měsíc");

    	$fromMonth = new DateTime();
    	$fromMonth->setTimestamp(strtotime("today"));
    	$fromMonth->sub(DateInterval::createFromDateString("1 year"));
	    $toMonth = new DateTime();
	    $toMonth->setTimestamp(strtotime("tomorrow")-1);
    	$thisNumberBoxes["YEAR"] = $this->prepareNumberBox($sensorNumbers, $workShift, $fromMonth, $toMonth, "Poslední rok");
	    return $thisNumberBoxes;
    }

    public function render(int $number, string $workShift)
    {
	    $thisNumberBox = $this->thisNumberBoxes($number, $workShift);
	    $this->template->numberBoxes = $thisNumberBox;

	    $this->template->render(__DIR__ . '/ThisStatusNumbersControl.latte');
    }

	/**
	 * @param Nette\Database\Table\Selection $sensorNumbers
	 * @param string $workShift
	 */
	public function renderAllOverview(Nette\Database\Table\Selection | null $sensorNumbers, string $workShift)
    {
	    $thisNumberBox = $this->numberBoxes($sensorNumbers, $workShift);
//	    dump($thisNumberBox);
	    $this->template->numberBoxes = $thisNumberBox;
//
	    $this->template->render(__DIR__ . '/ThisStatusNumbersControl.latte');

//	    dump($sensorNumbers);


    }

	/**
	 * @param Nette\Database\Table\Selection $sensorNumbers
	 * @param string $workShift
	 * @param string $pastTime
	 * @param string $dateText
	 */
	public function renderAllSingle(Nette\Database\Table\Selection $sensorNumbers, string $workShift, string $pastTime, string $dateText)
    {
	    $fromMonth = new DateTime();
	    $fromMonth->setTimestamp(strtotime("today"));
//	    $fromMonth->sub(DateInterval::createFromDateString("1 month"));
	    $fromMonth->sub(DateInterval::createFromDateString($pastTime));
	    $toMonth = new DateTime();
	    $toMonth->setTimestamp(strtotime("tomorrow")-1);
	    $numberBox = $this->prepareNumberBox($sensorNumbers, $workShift, $fromMonth, $toMonth, $dateText);

//	    dump($numberBox);
	    $this->template->numberBoxes = $numberBox;
//
	    $this->template->render(__DIR__ . '/ThisStatusNumbersControlSingle.latte');

    }


}