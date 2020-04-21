<?php

namespace App\CoreModule\Model;

use Nette;
use Nette\Database\Context;
use App\CoreModule\Model\SensorManager;

class ThisSensorManager
{
    use Nette\SmartObject;

    private $database;
    private $defaultMsgLanguage;
    private $defaultAPILanguage;
    private $sensorManager;

    public function __construct($defaultMsgLanguage,$defaultAPILanguage, Context $database, SensorManager $sensorManager)
    {
        $this->database = $database;
        $this->defaultMsgLanguage = $defaultMsgLanguage;
        $this->defaultAPILanguage = $defaultAPILanguage;
        $this->sensorManager = $sensorManager;
    }


    /**
     * Save sensor status to database
     * @param string $sensorName
     * @param mixed $state
     */
    public function addEvent($sensorName, $state)
    {
        if(!$this->sensorManager->sensorIsExist($sensorName))
        {
            return array(false, "Sensor with name ".$sensorName." delete does not exist", "Senzor s názvem".$sensorName." neexistuje");
        }

        if($succes = $this->database->table($sensorName)->insert([
            'state' => $state,
        ]))
        {            
            return array(true, "Event created", "Záznam byl vytvořen", $sensorName, $state);
        }
        else
        {
            return array(false, "ERROR!!!", "ERROR!!!");
        }
    }

    public function getAllEvents($sensorName)
    {
        return $this->database->table($sensorName)->fetchAll();
    }

    public function getAllEventsState($sensorName, $state)
    {
        return $this->database->table($sensorName)->where("state", $state)->fetchAll();
    }   
    
    public function getAllEventsOlder($sensorName, $time)
    {
        return $this->database->table($sensorName)->where("time >=?", $time)->fetch();
    } 

    public function getAllEventsYounger($sensorName, $time)
    {
        return $this->database->table($sensorName)->where("time <=?", $time)->fetchAll();
    }     
    
    public function countAllEvents($sensorName)
    {
        return $this->database->table($sensorName)->count();
    } 
    
    public function countAllEventsState($sensorName, $state)
    {
        return $this->database->table($sensorName)->where("state", $state)->count();
    }     
}


