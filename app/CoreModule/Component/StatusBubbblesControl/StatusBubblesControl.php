<?php

declare(strict_types=1);

namespace App\CoreModule\Component\StatusBubblesControl;

use App\CoreModule\Model\MultiSensorsManager;
use App\TimeManagers\TimeBox;
use App\Utils\BubblesPretty;
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
class StatusBubblesControl extends  Control{


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


	public function __construct(MultiSensorsManager $multiSensorsManager,
	                            ThisSensorManager $thisSensorManager,
								Context $database)
    {
	    $this->multiSensorsManager = $multiSensorsManager;
	    $this->thisSensorManager = $thisSensorManager;
	    $this->database = $database;
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
    	$x = $this->database->table("A".$number)->where("time>? AND state = ?", $from, $state)->count();
	    return $x;
    }

    public function getClassName(string $sting): string
    {
    	return "bubble-".strtolower($sting);
    }



    public function prepareBubbleBox(int $number, DateTime $from): BubblesPretty
    {
		if($lastEvent = $this->thisSensorManager->getLastEvent($number))
		{
			$lastEventState = $lastEvent->state;

			if($lastEvent->time>$from)
			{
				if($lastEventState == TimeBox::STOP)
				{
					$now  = new DateTime();
					return new BubblesPretty($lastEventState,
						$this->humanTime($now->getTimestamp() - $lastEvent->time->getTimestamp()),
						$this->getClassName($lastEventState));
				}
				else
				{
					$finishedPairs = ceil($this->getCountFinishedTodayWS($number, $from, "FINISHED")/2);

					if($finishedPairs>0)
					{
						if($lastEventState == TimeBox::OFF)
						{
							return new BubblesPretty($lastEventState, $finishedPairs." p", $this->getClassName($lastEventState));
						}
						else
						{
							return new BubblesPretty($lastEventState, $finishedPairs." p", $this->getClassName("finished"));
						}
					}
					else
					{
						return new BubblesPretty("EMPTY");
					}
				}
			}
			else
			{
				return new BubblesPretty("EMPTY");
			}

		}
		else
		{
			return new BubblesPretty("EMPTY");
		}

    }



    public function renderDay()
    {

    }

    public function render(array $roomAll, string $textName)
    {
	    $chartData = array();
	    $counter = 0;

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

//        $from="2020-04-24 08:00:00"; //For testing
//        $to="2020-04-24 23:00:00";
//        $now = new DateTime("2020-04-24 22:30:00");

	    $allSensorNumbers = $this->multiSensorsManager->getAllSensorsName();
	    $bubblesAll = array();
	    $row = 0;
	    $empty = 0;

	    $noData = true;


	    foreach($roomAll as $roomRow)
	    {
		    $bubblesRow = array();
	    	foreach($roomRow as $roomSensorNumber)
		    {

		    	if(array_key_exists($roomSensorNumber, $allSensorNumbers))
			    {
					$bubbleBox = $this->prepareBubbleBox(intval($roomSensorNumber), $from);
			    	$bubbleSensor = array($roomSensorNumber => $bubbleBox);
			    	$noData = false;
			    }
		    	else
			    {
				    $empty--;
				    $bubbleSensor = array($empty=>null);
			    }
		    	$bubblesRow += $bubbleSensor;
		    }
			$bubblesAll[$row] = $bubblesRow;
	    	$row++;
	    }

	    $this->template->textName = $textName;
	    $this->template->bubblesAll = $bubblesAll;
	    $this->template->noData = $noData;

//	    dump($empty);

//	    $allSensors = $this->multiSensorsManager->getAllSensorsEvents($roomSensorsArray, $from, $to, false);
//	    dump($url = $this->link("core:ahoj"));

    	$this->template->render(__DIR__ . '/StatusBubblesControl.latte');
    }

    public function handleClick()
    {
        echo "OK";
    }

}