<?php

namespace App\CoreModule\Model;

use http\Exception;
use Nette;
use Nette\Database\Context;
use App\CoreModule\Model\MultiSensorsManager;
use App\CoreModule\Model\ThisSensorManager;
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
        YEAR = "Y";


    private $database;
    private $thisSensorManager;

    public function __construct( Context $database, ThisSensorManager $thisSensorManager)
    {
        $this->database = $database;
        $this->thisSensorManager = $thisSensorManager;
    }

    /**
     * @brief Get count of rows in table
     * @param $number machine number
     * @return int count of rows
     */
    public function getCountSensors($number) :int
    {
        return $this->database->table("sensors")->where("number = ?", $number)->count();
//        try {
//            $this->database->table("sensors")->where("number = ?", $number)->count();
//        } catch (Nette\InvalidArgumentException $e)

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

	private function getSelectionTime(string $selection, DateTime $from)
	{

		switch ($selection)
		{
			case self::HOUR:
				$from->setTime($from->format("H"), 0, 0);
				$to = DateTime::from($from);
				$to->add(DateInterval::createFromDateString("1 hour"))->sub(DateInterval::createFromDateString("1 second"));
				break;

			case self::DAY:
				$from->setTime(0, 0, 0);
				$to = DateTime::from($from);
				$to->add(DateInterval::createFromDateString("1 day"))->sub(DateInterval::createFromDateString("1 second"));
				break;

			case self::MONTH:
				$from->setTime(0,0,0)->setDate($from->format("Y"), $from->format("m"), 1);
				$to = DateTime::from($from);
				$to->add(DateInterval::createFromDateString("1 month"))->sub(DateInterval::createFromDateString("1 second"));
				break;

			case self::YEAR:
				$from->setTime(0,0,0)->setDate($from->format("Y"), 1, 1);
				$to = DateTime::from($from);
				$to->add(DateInterval::createFromDateString("1 year"))->sub(DateInterval::createFromDateString("1 second"));
				break;

			default:
				$to = "2100-01-01 00:00:00";
		}

		return array("from"=>$from, "to"=>$to);
    }

    /**
     * @param $sNumber
     * @param $selection
     * @param DateTime $from
     * @return array
     */
    public function createSelection_H($sNumber, $selection, DateTime $from)
    {
        if(!$this->sensorIsExist($sNumber))
        {
            return Pretty::return(false, "", "Sensor with this name does not exist");
        }

        $sectionName = $this->getDbSelectionName($sNumber, $selection);



		$selectionTime = $this->getSelectionTime($selection, $from);
		$from = $selectionTime["from"];
		$to = $selectionTime["to"];


        $rawData = $this->thisSensorManager->getAllEvents($sNumber, $from, $to);
		$previousData = $this->thisSensorManager->getPreviousEvent($sNumber, $rawData);

	    $databaseOutput = new DatabaseSelection();

	    if($rawData)
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

	        $timeBox = new TimeBox($rawData, $from, $to);

	        $databaseOutput->t_stop += $timeBox->stopTime($previousData);
		    $databaseOutput->t_all += $timeBox->allTime($previousData);
		    $databaseOutput->t_work = $databaseOutput->t_all - $databaseOutput->t_stop;

	    }
	    else
	    {
		    return Pretty::return(false, $databaseOutput, "No input data");
	    }

	    if(!$this->database->table($sectionName)->where("time = ?", $from)->fetch())
	    {
		    $this->database->table($sectionName)->insert([
		        'time' => $from,
		        't_stop' => $databaseOutput->t_stop,
			    't_work' => $databaseOutput->t_work,
			    't_all' => $databaseOutput->t_all,
			    'c_FINISHED' => $databaseOutput->c_FINISHED,
			    'c_STOP' => $databaseOutput->c_STOP,
		    ]);

		    return Pretty::return(true, $databaseOutput, "Insert");
	    }
	    else
	    {
		    $this->database->table($sectionName)->where("time = ?", $from)->update([
			    'time' => $from,
			    't_stop' => $databaseOutput->t_stop,
			    't_work' => $databaseOutput->t_work,
			    't_all' => $databaseOutput->t_all,
			    'c_FINISHED' => $databaseOutput->c_FINISHED,
			    'c_STOP' => $databaseOutput->c_STOP,
		    ]);

		    return Pretty::return(true, $databaseOutput, "Update");
	    }


    }


    public function createSelection_DMY($sNumber, $selection, DateTime $from)
    {
	    if(!$this->sensorIsExist($sNumber))
	    {
		    return Pretty::return(false, "", "Sensor with this name does not exist");
	    }

	    if(($lowSelection = $this->getLowerSelection($selection))== "ERROR")
	    {
	    	return Pretty::return(false,"", "Input selection is not accepted, only (DAY, MONTH, YEAR)");
	    }

	    $lowDbSectionName = $this->getDbSelectionName($sNumber, $lowSelection);
	    $dbSelectionName = $this->getDbSelectionName($sNumber, $selection);


	    $selectionTime = $this->getSelectionTime($selection, $from);
	    $from = $selectionTime["from"];
	    $to = $selectionTime["to"];

	    $lowSelectionTime = $this->getSelectionTime($lowSelection, $from);
	    $lowFrom = $selectionTime["from"];
	    $lowTo = $selectionTime["to"];


	    $allData = $this->database->table($lowDbSectionName)->where("time >=? AND time <=?", $lowFrom, $lowTo)->fetchAll();


	    $databaseOutput = new DatabaseSelection();

	    if($allData)
	    {
		    foreach($allData as $data)
		    {
			    $databaseOutput->t_stop += $data->t_stop;
			    $databaseOutput->t_all += $data->t_all;
			    $databaseOutput->t_work += $data->t_work;
			    $databaseOutput->c_FINISHED += $data->c_FINISHED;
			    $databaseOutput->c_STOP += $data->c_STOP;
		    }
	    }
	    else
	    {
		    return Pretty::return(false, $databaseOutput, "No input data");
	    }

	    if(!$this->database->table($dbSelectionName)->where("time = ?", $from)->fetch())
	    {
		    $this->database->table($dbSelectionName)->insert([
			    'time' => $from,
			    't_stop' => $databaseOutput->t_stop,
			    't_work' => $databaseOutput->t_work,
			    't_all' => $databaseOutput->t_all,
			    'c_FINISHED' => $databaseOutput->c_FINISHED,
			    'c_STOP' => $databaseOutput->c_STOP,
		    ]);

		    return Pretty::return(true, $databaseOutput, "Insert");
	    }
	    else
	    {
		    $this->database->table($dbSelectionName)->where("time = ?", $from)->update([
			    'time' => $from,
			    't_stop' => $databaseOutput->t_stop,
			    't_work' => $databaseOutput->t_work,
			    't_all' => $databaseOutput->t_all,
			    'c_FINISHED' => $databaseOutput->c_FINISHED,
			    'c_STOP' => $databaseOutput->c_STOP,
		    ]);

		    return Pretty::return(true, $databaseOutput, "Update");
	    }





    }
}




