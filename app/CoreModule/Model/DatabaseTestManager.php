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


    public function saveEvent(int $number, string $state, DateTime $time): Pretty
    {
    	if(!$this->sensorsManager->sensorIsExist($number))
	    {
	    	return new Pretty(false, "", "Sensor number ".$number." not exist");
	    }

    	$this->database->table("A".$number)->insert([
    		'state'=>$state,
		    'time'=>$time
		]);

    	return new Pretty(true, "saveEvent", "OK");
    }

    public function saveRandom()
    {

    }

    public function saveStopRework(int $number, DateTime &$workTime, &$count)
    {
	    $this->waitMinutes(rand(1, 4), $workTime);
	    $this->saveEvent($number, TimeBox::STOP, $workTime);
//	    $workTime->add(DateInterval::createFromDateString(rand(1,30)." minutes"));
	    $count++;

	    $this->waitMinutes(rand(1, 30), $workTime);
	    $this->saveEvent($number, TimeBox::REWORK, $workTime);
//	    $workTime->add(DateInterval::createFromDateString(rand(1,3)." minutes"));
	    $count++;

    }

    public function waitMinutes(int $minutes, DateTime &$dateTime)
    {
    	$dateTime->add(DateInterval::createFromDateString($minutes." minutes ".rand(0, 59)." seconds"));
    }

    public function saveRandomDay(int $number, DateTime $startTime, DateTime $stopTime): Pretty
    {
	    if(!$this->sensorsManager->sensorIsExist($number))
	    {
		    return new Pretty(false, "saveRandomDay", "Sensor number ".$number." not exist");
	    }
//		$startTime = new $startDate;

//		$startTime->setTime(rand(5, 6), rand(1, 59), rand(1, 59));
    	$this->saveEvent($number, TimeBox::ON, $startTime);

	    $workTime = $startTime;
//	    $workTime->add(DateInterval::createFromDateString(rand(1,30)." minutes"));
//		$this->waitMinutes(rand(1, 30), $workTime);

	    $i = 0;

	    while($workTime < $stopTime)
	    {
		    $spA = rand(0, 4);
		    for($sp = 0; $sp< $spA; $sp++)
		    {
			    $this->saveStopRework($number, $workTime, $i);
		    }

		    $okA = rand(0, 10);
		    for($ok = 0; $ok< $okA; $ok++)
		    {
			    $this->waitMinutes(rand(2, 4), $workTime);
			    $this->saveEvent($number, TimeBox::FINISHED, $startTime);
			    $i++;
//			    $workTime->add(DateInterval::createFromDateString(rand(1,4)." minutes"));
		    }
	    }


//	    $workTime->add(DateInterval::createFromDateString(rand(1,30)." minutes"));
	    $this->waitMinutes(rand(2, 30), $workTime);

	    $this->saveEvent($number, TimeBox::OFF, $workTime);

	    return new Pretty(true, "saveRandomDay", "OK");
    }

}


