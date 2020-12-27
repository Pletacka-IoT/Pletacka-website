<?php

namespace App\CoreModule\Model;

use App\TimeManagers\TimeBox;
use App\Utils\DatabaseDataExtractorPretty;
use App\Utils\DatabaseSelection;
use App\Utils\DatabaseSelectionPretty;
use Cassandra\Date;
use mysql_xdevapi\DatabaseObject;
use Nette;
use Nette\Database\Context;
use App\CoreModule\Model\SensorsManager;
use DateInterval;
use DateTimeZone;
use Nette\Utils\DateTime;
//use DateTime;
use DateTimeImmutable;
use Nette\Database\UniqueConstraintViolationException;
use App\Utils\Pretty;


/**
 * @brief Work with one sensor by number
 */
class DatabaseTestManager
{
    use Nette\SmartObject;


	/**
	 * @var Context
	 */
	private $database;
	/**
	 * @var \App\CoreModule\Model\SensorsManager
	 */
	private $sensorsManager;
	/**
	 * @var ThisSensorManager
	 */
	private $thisSensorManager;
	/**
	 * @var DatabaseSelectionManager
	 */
	private $databaseSelectionManager;

	public function __construct(Context $database, SensorsManager $sensorsManager, ThisSensorManager $thisSensorManager, DatabaseSelectionManager $databaseSelectionManager)
    {

	    $this->database = $database;
	    $this->sensorsManager = $sensorsManager;
	    $this->thisSensorManager = $thisSensorManager;
	    $this->databaseSelectionManager = $databaseSelectionManager;
    }


	/**
	 * @brief Save new event to DB
	 * @param int $number
	 * @param string $state
	 * @param DateTime $time
	 * @param array $data
	 * @return Pretty
	 */
	public function saveEvent(int $number, string $state, DateTime $time, array &$data): Pretty
    {
    	$this->database->table("A".$number)->insert([
    		'state'=>$state,
		    'time'=>$time
		]);

    	$data[$state] ++;

    	return new Pretty(true, "saveEvent", "OK");
    }


	/**
	 * @brief Wait X minutes (DateTime reference)
	 * @param int $minutes
	 * @param DateTime $dateTime
	 */
	public function waitMinutes(int $minutes, DateTime &$dateTime)
    {
    	$dateTime->add(DateInterval::createFromDateString($minutes." minutes ".rand(0, 59)." seconds"));
//    	$dateTime->add(DateInterval::createFromDateString($minutes." minutes"));
    }

	/**
	 * @brief Generate random sensor data (works for one day)
	 * @param int $number
	 * @param DateTime $startTime start (morning) time
	 * @param DateTime $endTime end (night) time
	 * @return Pretty
	 */
	public function generateRandomDay(int $number, DateTime $startTime, DateTime $endTime): Pretty
    {
	    if(!$this->sensorsManager->sensorIsExist($number))
	    {
		    return new Pretty(false, "generateRandomDay", "Sensor number ".$number." not exist");
	    }
	    $allTimeMs = $endTime->getTimestamp()-$startTime->getTimestamp();
	    $stopTimeMs = $stopTimeStart = 0;
	    $workTime = clone $startTime;

	    $data = array();
	    $data["START_TIME"] = $startTime;
	    $data["END_TIME"] = $endTime;
	    $data[TimeBox::ON] = $data[TimeBox::OFF] = $data[TimeBox::STOP] = $data[TimeBox::REWORK] = $data[TimeBox::FINISHED] = 0;

	    $sState = TimeBox::ON;
	    while($workTime < $endTime)
	    {
			switch ($sState)
			{
				case TimeBox::ON:
					$this->saveEvent($number, TimeBox::ON, $workTime, $data);
					$this->waitMinutes(rand(2, 20), $workTime);

					if(rand(0, 3))
					{
						$sState = TimeBox::STOP;
					}
					else
					{
						$sState = TimeBox::FINISHED;
					}
					break;
				case TimeBox::OFF:
					$this->saveEvent($number, TimeBox::OFF, $workTime, $data);
					$stopTimeMs += $workTime->getTimestamp()-$stopTimeStart;
					$this->waitMinutes(rand(2, 30), $workTime);

					$sState = TimeBox::ON;
					break;
				case TimeBox::STOP:
					$this->saveEvent($number, TimeBox::STOP, $workTime, $data);
					$stopTimeStart = $workTime->getTimestamp();
					$this->waitMinutes(rand(1, 20), $workTime);

					$sState = TimeBox::REWORK;
					break;
				case TimeBox::REWORK:
					$this->saveEvent($number, TimeBox::REWORK, $workTime, $data);
					$stopTimeMs += $workTime->getTimestamp()-$stopTimeStart;
					$this->waitMinutes(rand(2, 4), $workTime);

					if(rand(0, 4))
					{
						$sState = TimeBox::FINISHED;
					}
					else
					{
						$sState = TimeBox::STOP;
					}
					break;
				case TimeBox::FINISHED:
					$this->saveEvent($number, TimeBox::FINISHED, $workTime, $data);
					$this->waitMinutes(rand(2, 4), $workTime);

					if(rand(0, 4))
					{
						$sState = TimeBox::FINISHED;
					}
					else
					{
						$sState = TimeBox::STOP;
					}
					break;
			}
	    }

	    $this->saveEvent($number, TimeBox::OFF, $endTime, $data);

	    if($sState == TimeBox::REWORK)
	    {
		    $stopTimeMs += $endTime->getTimestamp()-$stopTimeStart;
	    }

	    $data["STOP_TIME"] = $stopTimeMs;

	    return new Pretty(true, $data, "OK");
    }

	/**
	 * @brief Generate random sensor data from $startDate, for X days
	 * @param int $number
	 * @param DateTime $startDate uses only date
	 * @param int $days includes first day
	 * @return Pretty
	 */
	public function generateRandomDaysFromToCountDays(int $number, DateTime $startDate, int $days): Pretty
	{
		if(!$this->sensorsManager->sensorIsExist($number))
		{
			return new Pretty(false, "generateRandomDay", "Sensor number ".$number." not exist");
		}

		for($i = 0; $i<$days; $i++)
		{

			$startDate->setTime(rand(5, 6), rand(1, 59), rand(1, 59));

			$endTime = clone $startDate;
			$endTime->setTime(rand(20, 22), rand(1, 59), rand(1, 59));
			$gen = ($this->generateRandomDay($number,  $startDate, $endTime));

			$startDate->add(\DateInterval::createFromDateString("1 day"));
		}

		return new Pretty(true);

	}

//    public function generateRandomByFromToDate(int $number, DateTime $startTime, DateTime $endTime): Pretty
//    {
//	    $myTimeEnd = clone $startTime;
//	    $myTimeEnd->setTime(23, 59, 59);
//
//	    $this->generateRandomDay($number, $startTime, $myTimeEnd);
//	    $myTimeStart = new $myTimeEnd;
//	    $myTimeStart->setTime(0, 0);
////	    $myTimeStart->add(DateInterval::createFromDateString("1 day"));
////	    $myTimeEnd->add(DateInterval::createFromDateString("1 day"));
//
//	    while ($myTimeEnd <= $endTime)
//	    {
//		    $this->generateRandomDay($number, $myTimeStart, $myTimeEnd);
//		    $myTimeStart->add(DateInterval::createFromDateString("1 day"));
//		    $myTimeEnd->add(DateInterval::createFromDateString("1 day"));
//	    }
//
//
//	    $myTimeStart->add(DateInterval::createFromDateString("1 day"));
//	    $this->generateRandomDay($number, $myTimeStart, $endTime);
//
//	    return new Pretty(true, "OK");
//
//    }

//    public function testDatabaseTimeBox(int $number, DateTime $startTime, DateTime $stopTime)
//    {
//
//    }

}


