<?php

namespace App\Model;

use Nette;

class DatabaseManager
{
	use Nette\SmartObject;

    private $database;
    
    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }


    /**
     * Get settings from database 
     * @return array
     */
    public function getTitleSettings()
    {
        return $this->database->fetchField('SELECT * FROM settings ORDER BY id DESC LIMIT 1');
    }

    /**
     * Get count of rows in table
     */
    public function getCountSensors($name, $column = "name") :int
    {
        $result =  $this->database->query('SELECT * FROM sensors WHERE ' . $column . ' = ?', $name);
        return $result->getRowCount();
    }

    /**
     * Is sensor exist?
     */
    public function sensorIsExist($name, $column = "name") :bool
    {
        if($this->getCountSensors($name, $column)>0)
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }
    

    /**
     * Add new sensor
     * @param mixed $number machine number
     * @param mixed $name machine name
     * @param string $description machine description (optional)
     * @return array (bool - STATE, string - EN, string - CZ)
     */
    public function addNewSensor($number, $name, $description = "")
    {
        if($this->sensorIsExist($number, "number") )
        {
            return array(false, "Sensor with this number is exist", "Senzor s tímto číslem již existuje");
        }

        if($this->sensorIsExist($name, "name"))
        {
            return array(false, "Sensor with this name is exist", "Senzor s tímto názvem již existuje");
        }

        $result = $this->database->query('INSERT INTO sensors  ?', [ 
            'number' => $number,
            'name' => $name,
            'description' => $description,
        ]);

        return array($result, "Sensor created", "Senzor byl vytvořen");
    }


    /**
     * Add new sensor
     * @param mixed $number machine number
     * @param mixed $name machine name
     * @param string $description machine description (optional)
     * @return array (bool - STATE, string - EN, string - CZ)
     */
    public function editSensor($number, $name, $description = "")
    {
        if($this->sensorIsExist($number, "number") )
        {
            return array(false, "Sensor with this number is exist", "Senzor s tímto číslem již existuje");
        }

        if($this->sensorIsExist($name, "name"))
        {
            return array(false, "Sensor with this name is exist", "Senzor s tímto názvem již existuje");
        }

        $result = $this->database->query('UPDATE sensors  SET', [ 
            'number' => $number,
            'name' => $name,
            'description' => $description,
        ]);

        return array($result, "Sensor edited", "Senzor upraven vytvořen");
    }    
}
