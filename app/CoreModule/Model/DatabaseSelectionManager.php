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



    public function getSectionName(int $sNumber, $selection) :string
    {
        return "A".$sNumber."_".$selection;
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
    public function createSelection($sNumber, $selection, DateTime $from)
    {
        if(!$this->sensorIsExist($sNumber))
        {
            return Pretty::return(false, "", "Sensor with this name does not exist");
        }

        $selectionDatabase = $this->getSectionName($sNumber, $selection);



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


	    $this->database->table($selectionDatabase)->insert([
	    	'time' => $from,
	    	't_stop' => $databaseOutput->t_stop,
		    't_work' => $databaseOutput->t_work,
		    't_all' => $databaseOutput->t_all,
		    'c_FINISHED' => $databaseOutput->c_FINISHED,
		    'c_STOP' => $databaseOutput->c_STOP,
	    ]);

        return Pretty::return(true, $databaseOutput);
    }



}


