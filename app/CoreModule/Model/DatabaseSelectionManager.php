<?php

namespace App\CoreModule\Model;

use App\Utils\DatabaseDataExtractorPretty;
use App\Utils\DatabaseSelectionPretty;
use http\Exception;
use Nette;
use Nette\Database\Context;
use App\CoreModule\Model\MultiSensorsManager;
use App\CoreModule\Model\ThisSensorManager;
use App\CoreModule\Model\WorkShiftManager;
use DateInterval;
use DateTimeZone;
use Nette\Utils\DateTime;
use App\TimeManagers\TimeBox;
use DateTimeImmutable;
use Nette\Database\UniqueConstraintViolationException;
use App\Utils\Pretty;
use App\Utils\DatabaseSelection;


//class DatabaseOutput
//{
//	public $t_stop = 0;
//	public $t_work = 0;
//	public $c_FINISHED = 0;
//	public $c_STOP = 0;
//}



/**
 * @brief Manage work shifts
 */
class DatabaseSelectionManager
{
    use Nette\SmartObject;

    public const
        HOUR = "H",
        DAY = "D",
        MONTH = "M",
        YEAR = "Y",
	    HOUR_L = "hour",
		DAY_L = "day",
		MONTH_L = "month",
		YEAR_L = "year";


    public const
        WsA = "Cahovi",
	    WsB = "Vaňkovi";


    private $database;
    private $thisSensorManager;
	/**
	 * @var WorkShiftManager
	 */
	private $workShiftManager;

	public function __construct(Context $database,
	                            ThisSensorManager $thisSensorManager,
	                            WorkShiftManager $workShiftManager)
    {
        $this->database = $database;
        $this->thisSensorManager = $thisSensorManager;
	    $this->workShiftManager = $workShiftManager;
    }

    /**
     * @brief Get count of rows in table
     * @param $number machine number
     * @return int count of rows
     */
    public function getCountSensors($number) :int
    {
        return $this->database->table("sensors")->where("number = ?", $number)->count();
    }

    /**
     * @brief Is sensor exist?
     * @param $number machine number
     * @return bool
     */
    public function sensorIsExist($number) :bool
    {
        return $this->getCountSensors($number);

    }



    public function getDbSelectionName(int $sNumber, $selection) :string
    {
        return "A".$sNumber."_".$selection;
    }

    public function getLowerSelection(string $selection) :string
    {
		switch ($selection)
		{
			case self::HOUR:
				return "HourDatabase";

			case self::DAY:
				return self::HOUR;
				break;
			case self::MONTH:
				return self::DAY;
				break;
			case self::YEAR:
				return self::MONTH;
				break;
			default:
				return "ERROR";

		}
    }

	/**
	 *
	 * @param string $selection
	 * @param DateTime $from
	 * @return array
	 */
	private function getSelectionTime(string $selection, DateTime $from)
	{

		switch ($selection)
		{
			case self::HOUR:
				$from->setTime($from->format("H"), 0, 0);
				$to = DateTime::from($from);
				$to->add(DateInterval::createFromDateString("1 hour"));
				break;

			case self::DAY:
				$from->setTime(0, 0, 0);
				$to = DateTime::from($from);
				$to->add(DateInterval::createFromDateString("1 day"));
				break;

			case self::MONTH:
				$from->setTime(0,0,0)->setDate($from->format("Y"), $from->format("m"), 1);
				$to = DateTime::from($from);
				$to->add(DateInterval::createFromDateString("1 month"));
				break;

			case self::YEAR:
				$from->setTime(0,0,0)->setDate($from->format("Y"), 1, 1);
				$to = DateTime::from($from);
				$to->add(DateInterval::createFromDateString("1 year"));
				break;

			default:
				$to = "2100-01-01 00:00:00";
		}

		return array("from"=>$from, "to"=>$to);
    }


	/**
	 * @brief Generate database selection
	 * @param int $sNumber Sensor number
	 * @param string $selection Type of selection [self::H, D, M, Y]
	 * @param DateTime $from
	 * @return Pretty
	 */
	public function createSelection(int $sNumber,string $selection, DateTime $from) :Pretty
    {
	    if(!$this->sensorIsExist($sNumber))
	    {
		    return new Pretty(false, "", "Sensor with this name does not exist");
	    }

	    if(($lowSelection = $this->getLowerSelection($selection))== "ERROR")
	    {
	    	return new Pretty(false,"", "Input selection is not accepted, only (DAY, MONTH, YEAR)");
	    }


	    $dbSelectionName = $this->getDbSelectionName($sNumber, $selection);

	    $selectionTime = $this->getSelectionTime($selection, $from);
	    $from = $selectionTime["from"];
	    $to = $selectionTime["to"];

	    $ws = "";


	    $databaseOutput = new DatabaseSelection();
	    $databaseOutputB = new DatabaseSelection();


	    if ($lowSelection == "HourDatabase") //Generate hour database - with TimeBox
	    {
		    $rawData = $this->thisSensorManager->getAllEvents($sNumber, $from, $to);
		    $previousData = $this->thisSensorManager->getPreviousEvent($sNumber, $rawData);

		    $ws = $this->workShiftManager->getWSHour($from);


		    if(!$rawData)
		    {
			    return new Pretty(true, "Number:".$sNumber."; State:".$selection."; From:".$from, "No input data");
		    }
		    else
		    {
		    	foreach($rawData as $data)
			    {
				    if($data->state == "FINISHED")
				    {
					    $databaseOutput->c_FINISHED++;
				    }
				    if($data->state == "STOP")
				    {
					    $databaseOutput->c_STOP++;
				    }
			    }

			    $timeBox = new TimeBox($rawData, $previousData, $from, $to);

			    $databaseOutput->t_all += $timeBox->allTime();
			    $databaseOutput->t_stop += $timeBox->stopTime();
			    $databaseOutput->t_work = $databaseOutput->t_all - $databaseOutput->t_stop;

			    if(!$this->database->table($dbSelectionName)->where("time = ?", $from)->fetch())
			    {
				    $this->database->table($dbSelectionName)->insert([
					    'time' => $from,
					    'workShift' => $ws,
					    't_stop' => $databaseOutput->t_stop,
					    't_work' => $databaseOutput->t_work,
					    't_all' => $databaseOutput->t_all,
					    'c_FINISHED' => $databaseOutput->c_FINISHED,
					    'c_STOP' => $databaseOutput->c_STOP,
				    ]);

				    return new Pretty(true, $databaseOutput, "OK - Insert");
			    }
			    else
			    {
				    $this->database->table($dbSelectionName)->where("time = ?", $from)->update([
					    't_stop' => $databaseOutput->t_stop,
					    't_work' => $databaseOutput->t_work,
					    't_all' => $databaseOutput->t_all,
					    'c_FINISHED' => $databaseOutput->c_FINISHED,
					    'c_STOP' => $databaseOutput->c_STOP,
				    ]);

				    return new Pretty(true, $databaseOutput, "OK - Update");
			    }
		    }



	    }
	    else  //Generate day, month, year database - using addition lower database
	    {
		    $lowDbSectionName = $this->getDbSelectionName($sNumber, $lowSelection);


		    $allDataA = $this->database->table($lowDbSectionName)->where("time >=? AND time <=? AND workShift = ?", $from, $to, self::WsA)->fetchAll();
		    $allDataB = $this->database->table($lowDbSectionName)->where("time >=? AND time <=? AND workShift = ?", $from, $to, self::WsB)->fetchAll();

		    if(!($allDataA or $allDataB))
		    {
			    return new Pretty(true,"", "No input data");
		    }
		    else
		    {
			    foreach($allDataA as $data)
			    {
				    $databaseOutput->t_stop += $data->t_stop;
				    $databaseOutput->t_all += $data->t_all;
				    $databaseOutput->t_work += $data->t_work;
				    $databaseOutput->c_FINISHED += $data->c_FINISHED;
				    $databaseOutput->c_STOP += $data->c_STOP;
			    }
			    foreach($allDataB as $data)
			    {
				    $databaseOutputB->t_stop += $data->t_stop;
				    $databaseOutputB->t_all += $data->t_all;
				    $databaseOutputB->t_work += $data->t_work;
				    $databaseOutputB->c_FINISHED += $data->c_FINISHED;
				    $databaseOutputB->c_STOP += $data->c_STOP;
			    }


			    if(!$this->database->table($dbSelectionName)->where("time = ? AND workShift = ?", $from, self::WsA)->fetch())
			    {
				    $this->database->table($dbSelectionName)->insert([
					    'time' => $from,
					    'workShift'=> self::WsA,
					    't_stop' => $databaseOutput->t_stop,
					    't_work' => $databaseOutput->t_work,
					    't_all' => $databaseOutput->t_all,
					    'c_FINISHED' => $databaseOutput->c_FINISHED,
					    'c_STOP' => $databaseOutput->c_STOP,
				    ]);
			    }
			    else
			    {
				    $this->database->table($dbSelectionName)->where("time = ? AND workShift = ?", $from, self::WsA)->update([
					    't_stop' => $databaseOutput->t_stop,
					    't_work' => $databaseOutput->t_work,
					    't_all' => $databaseOutput->t_all,
					    'c_FINISHED' => $databaseOutput->c_FINISHED,
					    'c_STOP' => $databaseOutput->c_STOP,
				    ]);
			    }

			    if(!$this->database->table($dbSelectionName)->where("time = ? AND workShift = ?", $from, self::WsB)->fetch())
			    {
				    $this->database->table($dbSelectionName)->insert([
					    'time' => $from,
					    'workShift'=> self::WsB,
					    't_stop' => $databaseOutputB->t_stop,
					    't_work' => $databaseOutputB->t_work,
					    't_all' => $databaseOutputB->t_all,
					    'c_FINISHED' => $databaseOutputB->c_FINISHED,
					    'c_STOP' => $databaseOutputB->c_STOP,
				    ]);
				    return new Pretty(true, "Number:".$sNumber."; State:".$selection."; From:".$from, "OK - Insert");

			    }
			    else
			    {
				    $this->database->table($dbSelectionName)->where("time = ? AND workShift = ?", $from, self::WsB)->update([
					    't_stop' => $databaseOutputB->t_stop,
					    't_work' => $databaseOutputB->t_work,
					    't_all' => $databaseOutputB->t_all,
					    'c_FINISHED' => $databaseOutputB->c_FINISHED,
					    'c_STOP' => $databaseOutputB->c_STOP,
				    ]);

			        return new Pretty(true, "Number:".$sNumber."; State:".$selection."; From:".$from, "OK - Update");

			    }
		    }
	    }
    }

	/**
	 * @param int $number
	 * @param string $selection long format (DAY_L);
	 * @param DateTime $from
	 * @param DateTime $to
	 * @return Pretty
	 */
	public function createSelectionFromTo(int $number, string $selection, DateTime $from, DateTime $to) :Pretty
	{
		if($from>$to)
		{
			return new Pretty(false, null, "Bad time format");
		}
		$v = strlen($selection);
		if($v<3)
		{
			return new Pretty(false, null, "Bad selection format, use long format (DAY_L)");
		}


		$error = 0;
		$ok = 0;
		$state = true;
		$date = clone $from;
		while($date<=$to)
		{
			if(($returnMessage =  $this->createSelection($number, strtoupper($selection[0]), $date))->state)
			{
				$ok++;
			}
			else
			{
				$error++;
				$state = false;
			}
			$date->add(DateInterval::createFromDateString(("1 ".$selection)));

		}


		return new Pretty($state, array("ok"=>$ok, "error"=>$error), $selection." selection -> "."OK: ".$ok."; ERROR: ".$error);
	}

	public function createMultiSelection(int $number, DateTime $from, DateTime $to) :Pretty
	{
		$returnAll = array("ok"=>0, "error"=>0);

		$return = $this->createSelectionFromTo($number, DatabaseSelectionManager::HOUR_L, $from, $to);
		$this->countReturn($returnAll, $return->main);
		$return = $this->createSelectionFromTo($number, DatabaseSelectionManager::DAY_L, $from, $to);
		$this->countReturn($returnAll, $return->main);
		$return = $this->createSelectionFromTo($number, DatabaseSelectionManager::MONTH_L, $from, $to);
		$this->countReturn($returnAll, $return->main);
		$return = $this->createSelectionFromTo($number, DatabaseSelectionManager::YEAR_L, $from, $to);
		$this->countReturn($returnAll, $return->main);



		if($returnAll["error"]>0)
		{
			return new Pretty(true, array("ok"=>$returnAll["ok"], "error"=>$returnAll["error"]), $returnAll["error"]." errorů");
		}
		else
		{
			return new Pretty(true, array("ok"=>$returnAll["ok"], "error"=>$returnAll["error"]), "Aktualizováno ".$returnAll["ok"]." dat");
		}

	}

	private function countReturn(array& $returnAll, $return)
	{
		if($return != null)
		{
			$returnAll["ok"] += $return["ok"];
			$returnAll["error"] += $return["error"];
		}

	}


    public function createSelections(object $sensors, string $selection, DateTime $from) :Pretty
    {
    	if(!$sensors)
    		return new Pretty(false, "", "No sensors");

	    $returnJson = array();
	    $returnState = true;

	    foreach ($sensors as $sensor)
	    {
		    $ret = $this->createSelection(intval($sensor->number), $selection, $from);

		    if($ret->state)
		    {
			    $returnJson[$sensor->number] = array("state"=>true, "msg"=>$ret->msg, "number"=>$sensor->number, "selection"=>$selection, "from"=>$from);
		    }
		    else
		    {
		    	$returnState = false;
			    $returnJson[$sensor->number] = array("state"=>false, "msg"=>$ret->msg, "number"=>$sensor->number, "selection"=>$selection, "from"=>$from);
		    }
	    }

	    if($returnState)
	    {
		    return new Pretty(true, $returnJson, "OK");
	    }
	    else
	    {
		    return new Pretty(false, $returnJson, "ERROR");
	    }
    }


	/**
	 * @param int $number
	 * @param string $selection [self::HOUR, DAY, MONTH, YEAR]
	 * @param string $workShift ["Cahovi" or "Vaňkovi"]
	 * @param DateTime $from
	 * @param DateTime $to
	 * @return DatabaseDataExtractorPretty
	 */
	public function getSelectionData(int $number, string $selection, string $workShift, DateTime $from, DateTime $to): DatabaseDataExtractorPretty
    {
    	$dsPretty = new DatabaseDataExtractorPretty($number);
	    $dsPretty->workShift = $workShift;

    	$dSelection = $this->database->table("A".$number."_".$selection)->where("time >= ? AND time < ? AND workShift = ?", $from, $to, $workShift)->fetchAll();

    	if(!$dSelection)
	    {
	    	return new DatabaseDataExtractorPretty(-555, false, "No input data");
	    }

    	foreach ($dSelection as $dRow)
	    {
		    $dsPretty->stopTime += $dRow->t_stop;
		    $dsPretty->workTime += $dRow->t_work;
		    $dsPretty->allTime += $dRow->t_all;
		    $dsPretty->finishedCount += $dRow->c_FINISHED;
		    $dsPretty->stopCount += $dRow->c_STOP;
		    $dsPretty->status = true;
	    }

    	return $dsPretty;
    }


	/**
	 * @param int $number
	 * @param string $selection [self::HOUR, DAY, MONTH, YEAR]
	 * @param $workShift ["Cahovi" or "Vaňkovi" / null (auto)]
	 * @param DateTime $from
	 * @param DateTime $to
	 * @return array
	 */
	public function getSelectionDataDetail(int $number, string $selection, $workShift, DateTime $from, DateTime $to): array
    {

	    $dsPrettyArray = array();

	    $min = null;
	    $max = null;

	    if($workShift == null)
	    {
		    $dSelection = $this->database->table("A".$number."_".$selection)->where("time >= ? AND time < ?", $from, $to)->fetchAll();
	    }
	    else
	    {
		    $dSelection = $this->database->table("A".$number."_".$selection)->where("time >= ? AND time < ? AND workShift = ?", $from, $to, $workShift)->fetchAll();
	    }



    	if(!$dSelection)
	    {
	    	return $dsPrettyArray;
	    }

    	foreach ($dSelection as $dRow)
	    {
		    $dsPretty = new DatabaseDataExtractorPretty($number);
		    $dsPretty->workShift = $dRow->workShift;
		    $dsPretty->from = $dRow->time;

	    	$dsPretty->stopTime = $dRow->t_stop;
		    $dsPretty->workTime = $dRow->t_work;
		    $dsPretty->allTime = $dRow->t_all;
		    $dsPretty->finishedCount = $dRow->c_FINISHED;
		    $dsPretty->stopCount = $dRow->c_STOP;

		    if($dRow->c_STOP)
		    {
			    $dsPretty->stopTimeAvg = intval($dRow->t_stop/$dRow->c_STOP);
		    }

		    if($dRow->c_FINISHED)
		    {
			    $dsPretty->workTimeAvg = intval($dRow->t_work/$dRow->c_FINISHED);
		    }

		    $dsPretty->status = true;

//		    if($)

		    array_push($dsPrettyArray, $dsPretty);
	    }

    	return $dsPrettyArray;
    }
}




