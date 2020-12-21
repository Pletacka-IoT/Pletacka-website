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


    public function saveEvent(int $number, string $state, DateTime $time, array &$data): Pretty
    {
    	$this->database->table("A".$number)->insert([
    		'state'=>$state,
		    'time'=>$time
		]);

    	$data[$state] ++;

    	return new Pretty(true, "saveEvent", "OK");
    }


//    public function saveStopRework(int $number, DateTime &$workTime, &$count)
//    {
//	    $this->waitMinutes(rand(1, 4), $workTime);
//	    $this->saveEvent($number, TimeBox::STOP, $workTime);
////	    $workTime->add(DateInterval::createFromDateString(rand(1,30)." minutes"));
//	    $count++;
//
//	    $this->waitMinutes(rand(1, 30), $workTime);
//	    $this->saveEvent($number, TimeBox::REWORK, $workTime);
////	    $workTime->add(DateInterval::createFromDateString(rand(1,3)." minutes"));
//	    $count++;
//
//    }

    public function waitMinutes(int $minutes, DateTime &$dateTime)
    {
//    	$dateTime->add(DateInterval::createFromDateString($minutes." minutes ".rand(0, 59)." seconds"));
    	$dateTime->add(DateInterval::createFromDateString($minutes." minutes"));
    }

    public function generateRandomDay(int $number, DateTime $startTime, DateTime $stopTime): Pretty
    {
	    if(!$this->sensorsManager->sensorIsExist($number))
	    {
		    return new Pretty(false, "generateRandomDay", "Sensor number ".$number." not exist");
	    }
	    $allTimeMs = $stopTime->getTimestamp()-$startTime->getTimestamp();
	    $stopTimeMs = $stopTimeStart = 0;
	    $workTime = clone $startTime;

	    $data = array();
	    $data["START_TIME"] = $startTime;
	    $data["END_TIME"] = $stopTime;
	    $data[TimeBox::ON] = $data[TimeBox::OFF] = $data[TimeBox::STOP] = $data[TimeBox::REWORK] = $data[TimeBox::FINISHED] = 0;

	    $sState = TimeBox::ON;
	    while($workTime < $stopTime)
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

	    $this->saveEvent($number, TimeBox::OFF, $stopTime, $data);

	    if($sState == TimeBox::REWORK)
	    {
		    $stopTimeMs += $stopTime->getTimestamp()-$stopTimeStart;
	    }

	    $data["STOP_TIME"] = $stopTimeMs;

	    return new Pretty(true, $data, "OK");
    }

//    public function generateRandomByDates(int $number, DateTime $startTime, DateTime $endTime): Pretty
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


