<?php

declare(strict_types=1);

namespace App\CoreModule\Component\StatusNumbersControl;

use App\CoreModule\Model\MultiSensorsManager;
use App\TimeManagers\TimeBox;
use App\Utils\BubblesPretty;
use App\Utils\NumbersPreparePretty;
use App\Utils\Pretty;
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
    	return $this->database->table("A".$number)->where("time>? AND state = ?", $from, $state)->count();
    }

    public function getClassName(string $sting): string
    {
    	return "bubble-".strtolower($sting);
    }



    public function prepareNumberBox(array $allSensorsNumbers, DateTime $from, DateTime $to): BubblesPretty
    {
		foreach ($allSensorsNumbers as $sensorNumber)
		{
			$sensorNumberData = new NumbersPreparePretty($sensorNumber);



		}



    	return new BubblesPretty("");
    }



    public function renderDay()
    {

    }

    public function render(array $rooms)
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
				    else
				    {

				    }

			    }

		    }
	    }

	    $numbersAll = $this->prepareNumberBox($numbersAllUsed, $from, $to);
		echo"";
//	    $this->template->textName = $textName;
    	$this->template->render(__DIR__ . '/StatusNumbersControl.latte');
    }

    public function handleClick()
    {
        echo "OK";
    }

}