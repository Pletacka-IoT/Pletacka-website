<?php

declare(strict_types=1);

namespace App\CoreModule\Component\StatusBubblesControl;

use App\CoreModule\Model\MultiSensorsManager;
use App\Utils\BubblesPretty;
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


	public function __construct(MultiSensorsManager $multiSensorsManager,
	                            ThisSensorManager $thisSensorManager)
    {
	    $this->multiSensorsManager = $multiSensorsManager;
	    $this->thisSensorManager = $thisSensorManager;
    }

     public function prepareBubbleBox(int $number): BubblesPretty
     {
		if($lastEvent = $this->thisSensorManager->getLastEvent($number))
		{
			return new BubblesPretty($lastEvent->state, 55, "class-".strtolower($lastEvent->state));

		}
		else
		{
			return new BubblesPretty("EMPTY");
		}

     }



    public function renderDay()
    {

    }

    public function render(array $roomAll)
    {
	    $chartData = array();
	    $counter = 0;

	    if(date("H")<14)
	    {
		    $from = date("Y-m-d 04:00:00");
		    $to = date("Y-m-d 14:00:00");
	    }
	    else
	    {
		    $from = date("Y-m-d 14:00:00");
		    $to = date("Y-m-d 23:59:00");
	    }
	    $now = new DateTime();

//        $from="2020-04-24 08:00:00"; //For testing
//        $to="2020-04-24 23:00:00";
//        $now = new DateTime("2020-04-24 22:30:00");

	    $allSensorNumbers = $this->multiSensorsManager->getAllSensorsName();
	    $bubblesAll = array();
	    $row = 0;
	    $empty = -1;



	    foreach($roomAll as $roomRow)
	    {
		    $bubblesRow = array();
	    	foreach($roomRow as $roomSensorNumber)
		    {

		    	if(array_key_exists($roomSensorNumber, $allSensorNumbers))
			    {
					$bubbleBox = $this->prepareBubbleBox(intval($roomSensorNumber));
			    	$bubbleSensor = array($roomSensorNumber => $bubbleBox);
			    }
		    	else
			    {
			    	$bubbleSensor = array($empty=>null);
			    	$empty--;
			    }
		    	$bubblesRow += $bubbleSensor;
		    }
			$bubblesAll[$row] = $bubblesRow;
	    	$row++;
	    }
	    $this->template->bubblesAll = $bubblesAll;

//	    $allSensors = $this->multiSensorsManager->getAllSensorsEvents($roomSensorsArray, $from, $to, false);
		$x= new BubblesPretty("ASD", 55, "aws");
	    $this->template->bub = $x;
	    dump($x);

    	$this->template->render(__DIR__ . '/StatusBubblesControl.latte');
    }

    public function handleClick()
    {
        echo "OK";
    }

}