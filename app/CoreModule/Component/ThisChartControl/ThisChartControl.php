<?php

declare(strict_types=1);

namespace App\CoreModule\Component\ThisChartControl;

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
class ThisChartControl extends  Control{


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

	public function stringToDateTime(string $text)
	{
		$dateTime = array();

		$dateTimeExplode = explode(",", $text);

		$dateTime["to"] = new DateTime();
		$dateTime["from"] = new DateTime();
		$dateTime["from"]->sub(DateInterval::createFromDateString($text));
		$dateTime["from"]->setTime(0, 0);
		return $dateTime;
	}


	private function prepareThisChart(int $number, $from, $to)
	{
		return array("chartWsA"=>"Cah", "chartWsB"=>"Van");


	}


	public function render(int $number, string $type, string $time, string $name = "", string $nameTime = "")
    {

	    $dateTime = $this->stringToDateTime($time);

    	$chats = $thisNumberBox = $this->prepareThisChart($number, $dateTime["from"], $dateTime["to"]);


    	$this->template->chartWsA = $chats["chartWsA"];
    	$this->template->chartWsB = $chats["chartWsB"];


	    $this->template->name = $name;
	    $this->template->nameTime = $nameTime;
    	$this->template->render(__DIR__ . '/ThisChartControl.latte');
		dump($thisNumberBox);
    }

}