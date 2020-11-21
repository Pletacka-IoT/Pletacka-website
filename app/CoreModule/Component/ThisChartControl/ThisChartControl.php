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
								DatabaseSelectionManager $databaseSelectionManager,
								WorkShiftManager $workShiftManager)
    {
	    $this->multiSensorsManager = $multiSensorsManager;
	    $this->thisSensorManager = $thisSensorManager;
	    $this->database = $database;
	    $this->databaseSelectionManager = $databaseSelectionManager;
	    $this->workShiftManager = $workShiftManager;
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

	public function prepareValue(array $chartDataRaw, string $type): array
	{
		$chartData = array();
		$min = null;
		$max = null;

		$first = true;

		foreach($chartDataRaw as $data)
		{
			$x = 5;
			switch ($type)
			{
				case "finishedCount":
					$hour = array("from"=>$data->from, "value"=>$data->finishedCount);
					if($first)
					{
						$min = $hour["value"];
						$max = $hour["value"];
						$first = false;
					}

					if($hour["value"]>$max)
						$max = $hour["value"];
					if($hour["value"]<$min)
						$min = $hour["value"];
					array_push($chartData, $hour);
					break;

				case "stopCount":
					$value = $data->stopCount;
					break;

				case "stopTimeAvg":
					$value = $data->stopTimeAvg;
					break;


			}
		}

		$chartDataAll = array();
		$chartDataAll["min"] = $min;
		$chartDataAll["max"] = $max;
		$chartDataAll["data"] = $chartData;


		return $chartDataAll;
	}


	public function prepareThisChartHour(int $number, string $workShift, string $type, DateTime $from, DateTime $to, string $color, string $name): DateChart
	{
		$name = explode("-", $name);

		$chartDataRaw = $this->databaseSelectionManager->getSelectionDataDetail($number, DatabaseSelectionManager::HOUR, $workShift, $from, $to);

		$chartData = $this->prepareValue($chartDataRaw, $type);

//		$chartData = $chartDataRaw;


		$serieType = DateSerie::AREA_SPLINE;
		$dayChart = new DateChart();
		$dayChart->enableTimePrecision(); // Enable time accurate to seconds
		$dayChart->setMinValue($chartData["min"]-1);
		$dayChart->setMaxValue($chartData["max"]+1);
		$dayChart->setValueSuffix($name[1]);

//		$x = new DateTime;
//		$x->time



		$serie = new DateSerie($serieType, $name[0], $color);
		$first = true;
		foreach($chartData["data"] as $data)
		{
			$serie->addSegment(new DateSegment(DateTimeImmutable::createFromMutable($data["from"]), $data["value"]));
		}
		$dayChart->addSerie($serie);

		return $dayChart;

	}
	


	private function prepareThisChartPair(int $number, string $type, DateTime $from, DateTime $to, string $name, string $color = null)
	{

		if(!$color)
		{
			$color = dechex(rand(0x000000, 0xFFFFFF));
		}



		$ws = $this->workShiftManager->getWeekWS();

		$chartWsA = $this->prepareThisChartHour($number, $ws[0], $type, $from, $to, $color, $name);

		$chartWsB = $this->prepareThisChartHour($number, $ws[1], $type, $from, $to, $color, $name);


		return array("chartWsA"=>$chartWsA, "chartWsB"=>$chartWsB);
	}





	public function render(int $number, string $type, string $time, string $name = "", string $nameTime = "", string $color = null)
    {

	    $dateTime = $this->stringToDateTime($time);

    	$chats = $thisNumberBox = $this->prepareThisChartPair($number, $type, $dateTime["from"], $dateTime["to"], $name, $color);


    	$this->template->chartWsA = $chats["chartWsA"];
    	$this->template->chartWsB = $chats["chartWsB"];


	    $this->template->name = $name;
	    $this->template->nameTime = $nameTime;
    	$this->template->render(__DIR__ . '/ThisChartControl.latte');
		dump($thisNumberBox);
    }

    public function renderWs(string $ws)
    {
    	dump($ws);
    }

}