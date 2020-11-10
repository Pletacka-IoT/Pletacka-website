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
	 * @param int $number
	 * @param DateTime $from
	 * @param DateTime $to
	 * @return DatabaseDataExtractorPretty
	 */
	public function getTimeBoxData(int $number, DateTime $from, DateTime $to):DatabaseDataExtractorPretty
    {
	    $databaseDate = new DatabaseDataExtractorPretty;
	    $databaseDate->from = $from;
	    $databaseDate->to = $to;


	    $sensorEvents = $this->thisSensorManager->getAllEvents($number, $from, $to);
	    if($sensorEvents)
	    {
		    $databaseDate->status = true;
		    $databaseDate->msg = "ok";
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
	 * @brief Get data to hour after
	 * @param int $number
	 * @param DateTime $from
	 * @param DateTime $toTest
	 * @return DatabaseDataExtractorPretty
	 * @throws \Exception
	 */
	public function getDataToHourAfter(int $number, DateTime $from, DateTime $toTest):DatabaseDataExtractorPretty
	{
		$to = new DateTime($from);
		$to->setTime(intval($from->format("H"))+1, 0);
		if($to > $toTest)
		{
			$out = $this->getTimeBoxData($number, $from, $toTest);
			$out->continueNext = false;
			return $out;
		}

		return $this->getTimeBoxData($number, $from, $to);
    }


	/**
	 * @brief Get data to hour before
	 * @param int $number
	 * @param DateTime $to
	 * @return DatabaseDataExtractorPretty
	 */
	private function getDataToHourBefore(int $number, DateTime $to):DatabaseDataExtractorPretty
	{
		$from = new DateTime($to);
		$from->setTime(intval($to->format("H")), 0);

		return $this->getTimeBoxData($number, $from, $to);
    }
    
//    private function addTime(DatabaseDataExtractorPretty $base, DatabaseDataExtractorPretty $add): DatabaseDataExtractorPretty
//    {
//    	$base->
//    }

//	/**
//	 * @param int $number
//	 * @param DateTime $from
//	 * @param DateTime $to
//	 * @return DatabaseDataExtractorPretty
//	 * @throws \Exception
//	 */
//	public function getDataHourly(int $number, DateTime $from, DateTime $to):DatabaseDataExtractorPretty
//	{
//		if($to<$from)
//		{
//			return new DatabaseDataExtractorPretty(false, "Bad time format (from>to)");
//		}
//
//		$databaseDate = new DatabaseDataExtractorPretty;
//
//		$dataToHourAfter = $this->getDataToHourAfter($number, $from, $to);
//		
//
//		if($dataToHourAfter->stopThere)
//		{
//			return $databaseDate;
//		}
//		$selectionFrom = $dataToHourAfter->to;
//		echo "";
//
//		$dataToHourBefore = $this->getDataToHourBefore($number, $to, $from);
//		$selectionTo = $dataToHourAfter->from;
//
////		$this->
//		return $this->getTimeBoxData($number, $from, $to);
//	}

//	public function getDataHourly(int $number, DateTime $from, DateTime $to):DatabaseDataExtractorPretty
//	{
//		$this->databaseSelectionManager->getSelectionData($number, DatabaseSelectionManager::HOUR, )
//	}


}


