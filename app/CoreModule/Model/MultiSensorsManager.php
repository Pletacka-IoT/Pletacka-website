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
        foreach($sensorsNumbers as $sensor)
        {
            if($inputFromDatabase)
            {
                $allSensors += array($sensor->number => $this->thisSensorManager->getAllEvents($sensor->number, $from, $to));
            }
            else
            {
                $allSensors += array($sensor => $this->thisSensorManager->getAllEvents($sensor, $from, $to));
            }
        }
        return $allSensors;
    }
}
