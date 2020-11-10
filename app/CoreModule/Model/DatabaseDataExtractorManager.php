<?php

namespace App\CoreModule\Model;

use App\TimeManagers\TimeBox;
use App\Utils\DatabaseDataExtractorPretty;
use App\Utils\DatabaseSelection;
use App\Utils\DatabaseSelectionPretty;
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
class DatabaseDataExtractorManager
{
    use Nette\SmartObject;


	public const
        START = "2000-01-01 00:00:00",
        MINUTE = 60;

    public const
        FINISHED = 'FINISHED',  // Machine is working
        STOP = "STOP",	        // Machine is not working
        REWORK = "REWORK", 	    // State after end of STOP
        ON = 'ON',              // ON machine
        OFF = 'OFF';            // OFF machine



    private $database;
    private $sensorsManager;
	/**
	 * @var ThisSensorManager
	 */
	private $thisSensorManager;

	public function __construct(Context $database, SensorsManager $sensorsManager, ThisSensorManager $thisSensorManager)
    {
        $this->database = $database;
        $this->sensorsManager = $sensorsManager;
	    $this->thisSensorManager = $thisSensorManager;
    }


	/**
	 * @brief Get data to hour after
	 * @param int $number
	 * @param DateTime $from
	 * @return DatabaseDataExtractorPretty
	 * @throws \Exception
	 */
	public function getDataToHourAfter(int $number, DateTime $from):DatabaseDataExtractorPretty
	{
		$to = new DateTime($from);
		$to->setTime(intval($from->format("H"))+1, 0);

		$databaseDate = new DatabaseDataExtractorPretty($number);
		$databaseDate->number = $number;

		$sensorEvents = $this->thisSensorManager->getAllEvents($number, $from, $to);
		if($sensorEvents)
		{
			$databaseDate->status = true;
			$previousEvent = $this->thisSensorManager->getPreviousEvent($number, $sensorEvents);

			$timeBox = new TimeBox($sensorEvents, $previousEvent, $from, $to);
			$databaseDate->stopTime = $timeBox->stopTime();
			$databaseDate->allTime = $timeBox->allTime();
			$databaseDate->workTime = $timeBox->workTime($databaseDate->allTime, $databaseDate->stopTime);

			$databaseDate->finishedCount = $timeBox->countEvents(TimeBox::FINISHED);
			$databaseDate->stopCount = $timeBox->countEvents(TimeBox::STOP);
		}

		return $databaseDate;
    }


	/**
	 * @brief Get data to hour before
	 * @param int $number
	 * @param DateTime $from
	 * @return DatabaseDataExtractorPretty
	 * @throws \Exception
	 */
	public function getDataToHourBefore(int $number, DateTime $from):DatabaseDataExtractorPretty
	{
		$to = new DateTime($from);
		$to->setTime(intval($from->format("H"))+1, 0);

		$databaseDate = new DatabaseDataExtractorPretty($number);
		$databaseDate->number = $number;

		$sensorEvents = $this->thisSensorManager->getAllEvents($number, $from, $to);
		if($sensorEvents)
		{
			$databaseDate->status = true;
			$previousEvent = $this->thisSensorManager->getPreviousEvent($number, $sensorEvents);

			$timeBox = new TimeBox($sensorEvents, $previousEvent, $from, $to);
			$databaseDate->stopTime = $timeBox->stopTime();
			$databaseDate->allTime = $timeBox->allTime();
			$databaseDate->workTime = $timeBox->workTime($databaseDate->allTime, $databaseDate->stopTime);

			$databaseDate->finishedCount = $timeBox->countEvents(TimeBox::FINISHED);
			$databaseDate->stopCount = $timeBox->countEvents(TimeBox::STOP);
		}

		return $databaseDate;
    }


}


