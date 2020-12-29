<?php

namespace App\CoreModule\Model;

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
class ThisSensorManager
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

    public function __construct(Context $database, SensorsManager $sensorsManager)
    {
        $this->database = $database;
        $this->sensorsManager = $sensorsManager;

    }

    /**
     * @brief Add sensor status to database
     * @param string $sNumber
     * @param mixed  $state
     * @return Pretty
     */
    public function addEvent($sNumber, $state) :Pretty
    {
        if(!$this->sensorsManager->sensorIsExist($sNumber))
        {
            return new Pretty(0,"", "Senzor s názvem".$sNumber." neexistuje");
        }

        if($success = $this->database->table("A".$sNumber)->insert([
            'state' => $state,
        ]))
        {
            return new Pretty(true, array($sNumber, $state), "Záznam byl vytvořen");
        }
        else
        {
            return new Pretty(false, "ERROR!!!", "ERROR!!!");
        }
    }


    /**
     * @brief Get all machine events
     * @param        $sNumber
     * @param string $from
     * @param string $to
     * @return array
     */
    public function getAllEvents($sNumber, $from="2000-01-01 00:00:00" , $to="2100-01-01 00:00:00")
    {
        return $this->database->table("A".$sNumber)->where("time >=? AND time <=?", $from, $to)->fetchAll();
    }


	public function sensorHasData($sNumber): bool
	{
		return boolval($this->database->table("A".$sNumber)->fetch());
	}

	public function getPreviousEvent($sNumber, $events)
    {
        $previous = array_key_first($events)-1;
        return $this->database->table("A".$sNumber)->where("id =?",$previous)->fetch();
    }


	public function getLastEvent(int $sNumber)
	{
		return $this->database->table("A".$sNumber)->order("id DESC")->fetch();
	}

}


