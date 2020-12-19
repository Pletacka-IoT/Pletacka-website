<?php

declare(strict_types=1);

namespace App\CoreModule\Component\ThisChartControl;

use App\CoreModule\Model\DatabaseSelectionManager;
use App\CoreModule\Model\MultiSensorsManager;
use App\TimeManagers\TimeBox;
use App\Utils\BubblesPretty;
use App\Utils\ChartDataPretty;
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


//
//	/**
//	 * @param int $number
//	 * @param string $workShift
//	 * @param string $name
//	 * @param string $suffix
//	 * @param string $seriesType
//	 * @param string $color
//	 * @param bool $enableTimePrecision
//	 * @param int $from
//	 * @param int $to
//	 * @return ChartDataPretty
//	 */
//	public function prepareThisChartData(int $number, string $workShift, string $name, string $suffix, int $from, int $to, string $seriesType, string $color, bool $enableTimePrecision = false): ChartDataPretty
//	{
//		$chartData = new ChartDataPretty($name, $from, $to, $suffix);
//		$chartData->workShift = $workShift;
//		$chartData->number = $number;
//		$chartData->
//		$chartData->
//
//
//
//		$chartDataRaw = $this->databaseSelectionManager->getSelectionDataDetail($number, DatabaseSelectionManager::HOUR, $workShift, $from, $to);
//
//		$chartData = array();
//		$min = null;
//		$max = null;
//
//		$first = true;
//
//		$badType = false;
//
//		foreach($chartDataRaw as $data)
//		{
//
//			switch ($type)
//			{
//				case "finishedCount":
//					$hour = array("from"=>$data->from, "value"=>$data->finishedCount);
//					array_push($chartData, $hour);
//					break;
//
//				case "stopCount":
//					$hour = array("from"=>$data->from, "value"=>$data->stopCount);
//					array_push($chartData, $hour);
//					break;
//
//				case "stopTimeAvg":
//					$hour = array("from"=>$data->from, "value"=>$data->stopTimeAvg);
//					array_push($chartData, $hour);
//					break;
//
//				default:
//					$badType = true;
//					break;
//			}
//
//			if($badType)
//			{
//				break;
//			}
//		}
//
//		$chartDataAll = array();
//		$chartDataAll["min"] = $min;
//		$chartDataAll["max"] = $max;
//		$chartDataAll["data"] = $chartData;
//
//
//		return $chartDataAll;
//	}



	private function prepareThisChart(int $number, array $dateTime)
	{
		$chartDataRaw = $this->databaseSelectionManager->getSelectionDataDetail($number, DatabaseSelectionManager::HOUR, null, $dateTime["from"], $dateTime["to"]);

		return $chartDataRaw;
	}

//	private function rendThisChart(array $chartData, string $suffix)
//	{
//		$dayChart = new DateChart();
////		$dayChart->enableTimePrecision(); // Enable time accurate to seconds
//		$dayChart->setValueSuffix($suffix);
//
//		foreach ($chartData as $chartDataGroup) {
//			$serie = new DateSerie(DateSerie::AREA_SPLINE, $chartDataGroup["ws"], $chartDataGroup["color"]);
//			foreach($chartDataGroup["data"] as $chartDataEvent)
//			{
//				$serie->addSegment(new DateSegment(DateTimeImmutable::createFromMutable($chartDataEvent["time"]), $chartDataEvent["value"]));
//			}
//			$dayChart->addSerie($serie);
//		}
//
//		return $dayChart;
//	}
	private function getColourByWS(string $workShift)
	{
		switch($workShift)
		{
			case "Cahovi":
				$c = "orange";
				break;

			case "Vaňkovi":
				$c = "blue";
				break;
		}

		return $c;
	}


	private function rendThisChart(array $chartData, string $suffix)
	{
		$dayChart = new DateChart();
		$dayChart->enableTimePrecision(); // Enable time accurate to seconds
		$dayChart->setValueSuffix($suffix);

		$workShift = "";

		foreach ($chartData as $index => $chartDataGroup)
		{
			if($index == array_key_first($chartData))
			{
				$workShift = $chartDataGroup->workShift;
				$serie = new DateSerie(DateSerie::AREA_SPLINE, $chartDataGroup->workShift, $this->getColourByWS($workShift));
				$serie->addSegment(new DateSegment(DateTimeImmutable::createFromMutable($chartDataGroup->from), $chartDataGroup->finishedCount));
			}
			else
			{
				if($workShift == $chartDataGroup->workShift)
				{
					$serie->addSegment(new DateSegment(DateTimeImmutable::createFromMutable($chartDataGroup->from), $chartDataGroup->finishedCount));
				}
				else
				{
					$serie->addSegment(new DateSegment(DateTimeImmutable::createFromMutable($chartDataGroup->from), $chartDataGroup->finishedCount));
					$dayChart->addSerie($serie); // save last segment
					$workShift = $chartDataGroup->workShift;
					$serie = new DateSerie(DateSerie::AREA_SPLINE, $chartDataGroup->workShift, $this->getColourByWS($workShift));
					$serie->addSegment(new DateSegment(DateTimeImmutable::createFromMutable($chartDataGroup->from), $chartDataGroup->finishedCount));
				}
			}

//			$serie = new DateSerie(DateSerie::AREA_SPLINE, $chartDataGroup["ws"], $chartDataGroup["color"]);
//			foreach($chartDataGroup["data"] as $chartDataEvent)
//			{
//				$serie->addSegment(new DateSegment(DateTimeImmutable::createFromMutable($chartDataEvent["time"]), $chartDataEvent["value"]));
//			}
//			$dayChart->addSerie($serie);
		}

		return $dayChart;
	}


	public function render(int $number, string $type, string $time, string $name = "", string $suffix = "", string $timeText = "")
    {

	    $dateTime = $this->stringToDateTime($time);

		$chartData = $this->prepareThisChart($number, $dateTime);

//		dump($chartData);

    	$chart = $thisNumberBox = $this->rendThisChart($chartData, $suffix);

    	$this->template->chart = $chart;
//    	$this->template->chartWsB = "";//$chats["chartWsB"];
//
//
	    $this->template->name = $name;
	    $this->template->timeText = $timeText;
    	$this->template->render(__DIR__ . '/ThisChartControl.latte');
//		dump($thisNumberBox);

    }




}