<?php

namespace App\CoreModule\Model;


use Exception;
use Nette;
use Nette\Database\Context;
use App\Utils\Pretty;
use App\CoreModule\Model\ThisSensorManager;


class MultiSensorsManager
{
	use Nette\SmartObject;

    private $database;
    private $defaultMsgLanguage;
    private $defaultAPILanguage;
    private $thisSensorManager;
    
    public function __construct($defaultMsgLanguage,$defaultAPILanguage, Context $database, ThisSensorManager $thisSensorManager)
    {
        $this->database = $database;
        $this->defaultMsgLanguage = $defaultMsgLanguage;
        $this->defaultAPILanguage = $defaultAPILanguage;
        $this->thisSensorManager = $thisSensorManager;
    }

    /**
     * Get all sensors name
     * @return array
     */
    public function getAllSensorsName()
    {
        return $this->database->table("sensors")->order("number")->fetchAll();
    }

    /**
     * Get all sensors events
     * @param        $sensorsName
     * @param string $from
     * @param string $to
     */
    public function getAllSensorsEvents($sensorsName, $from="2000-01-01 00:00:00" , $to="2100-01-01 00:00:00")
    {
        $allSensors = array();
        foreach($sensorsName as $sensor)
        {
            $allSensors += array($sensor->name => $this->thisSensorManager->getAllEvents($sensor->name, $from, $to));
        }
        return $allSensors;
    }


    /**
     * @return mixed
     */
    public function getAPILanguage()
	{
		return $this->defaultAPILanguage;
    }

    /**
     * @return mixed
     */
    public function getMsgLanguage()
	{
		return $this->defaultMsgLanguage;
	}
}
