<?php

namespace App\CoreModule\Model;


use Exception;
use Nette;
use Nette\Database\Context;
use App\Utils\Pretty;
use App\CoreModule\Model\ThisSensorManager;

/**
 * @brief Work with all sensors
 */
class MultiSensorsManager
{
	use Nette\SmartObject;

    private $database;
    private $defaultMsgLanguage;
    private $defaultAPILanguage;
    private $thisSensorManager;
    
    public function __construct(Context $database, ThisSensorManager $thisSensorManager)
    {
        $this->database = $database;
        $this->thisSensorManager = $thisSensorManager;
    }

    /**
     * @brief Get all sensors name
     * @return array
     */
    public function getAllSensorsName()
    {
        return $this->database->table("sensors")->order("number")->fetchAll();
    }

    /**
     * @brief Get all sensors events
     * @param        $sensorsNumbers
     * @param string $from
     * @param string $to
     * @param bool   $inputFromDatabase default true (change for custom numbers array)
     * @return array
     */
    public function getAllSensorsEvents($sensorsNumbers, $from="2000-01-01 00:00:00" , $to="2100-01-01 00:00:00", $inputFromDatabase = true)
    {
        $allSensors = array();
        $lastState = null;
        foreach($sensorsNumbers as $sensor)
        {
            if($inputFromDatabase)
            {
                // Example $sensor = database output
                $rawEvents = $this->thisSensorManager->getAllEvents($sensor->number, $from, $to);
                $previousEvent = $this->thisSensorManager->getPreviousEvent($sensor->number, $rawEvents);

                if($lastKey = array_key_last($rawEvents)){$lastState = $rawEvents[array_key_last($rawEvents)]->state;}

                if($previousEvent){$previousEvent = $previousEvent->state;}

                $allSensors += array($sensor->number => array("raw" => $rawEvents, "previous" => $previousEvent, "last" => array_key_last($rawEvents),"from" => $from, "to" => $to));
            }
            else
            {
                // Example $sensor = array(1, 5, 8);
                $rawEvents = $this->thisSensorManager->getAllEvents($sensor, $from, $to);
                $previousEvent = $this->thisSensorManager->getPreviousEvent($sensor, $rawEvents);
                if($previousEvent){$previousEvent = $previousEvent->state;}

                if($lastKey = array_key_last($rawEvents)){$lastState = $rawEvents[array_key_last($rawEvents)]->state;}

                $allSensors += array($sensor => array("raw" => $rawEvents, "previous" => $previousEvent, "last" => $lastState, "from" => $from, "to" => $to));
            }
        }
        return $allSensors;
    }
}
