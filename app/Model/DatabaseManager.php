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
     * Edit sensor
     * @param mixed $oldname machine old name to edit
     * @param mixed $number machine number
     * @param mixed $name machine name
     * @param string $description machine description (optional)
     * @return array (bool - STATE, string - EN, string - CZ)
     */
    public function editSensor($oldName, $number, $name, $description = "")
    {
        if(!$this->sensorIsExist($oldName, "name") )
        {
            return array(false, "The sensor you want to edit does not exist", "Senzor který chceš upravit neexistuje");
        }

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
        ], 'WHERE name = ?', $oldName);

        return array($result, "Sensor edited", "Senzor upraven vytvořen");
    }    


    /**
     * Delete sensor
     * @param mixed $name machine name
     * @return array (bool - STATE, string - EN, string - CZ)
     */
    public function deleteSensor($name)
    {
        if(!$this->sensorIsExist($name, "name") )
        {
            return array(false, "The sensor you want to delete does not exist", "Senzor který chceš smazat neexistuje");
        }

        $result = $this->database->query('DELETE FROM sensors WHERE name = ?', $name);

        return array($result, "Sensor deleted", "Senzor byl smazán");
    }    


}
