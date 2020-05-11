<?php

namespace App\CoreModule\Model;

use Nette;
use Nette\Database\Context;


class SensorManager
{
	use Nette\SmartObject;

    private $database;
    private $defaultMsgLanguage;
    private $defaultAPILanguage;
    
    public function __construct($defaultMsgLanguage,$defaultAPILanguage, Context $database )
    {
        $this->database = $database;
        $this->defaultMsgLanguage = $defaultMsgLanguage;
        $this->defaultAPILanguage = $defaultAPILanguage;
    }


    /**
     * Get settings from database 
     * @return array
     */
    public function getTitleSettings()
    {
        return $this->database->table("settings")->get(1); //number is ID in table settings
        
    }


    /**
     * Get sensor from database 
     * @return array
     */    
    public function getSensorInfo($name)
    {
        return $this->database->table("sensors")->where("name", $name)[0];
    } 
    
    /**
     * Get all sensors from database 
     * @return array
     */    
    public function getSensors()
    {
        return $this->database->table("sensors");
    }  

    /**
     * Get sensor with specific number
     * @param string $number
     * @return null|\Nette\Database\Table\ActiveRow
     */
    public function getSensorsNumber($number)
    {
        return $this->database->table("sensors")->where("number", $number )->fetch();
    } 

    /**
     * Get sensor with specific name
     * @param string $name
     * @return null|\Nette\Database\Table\ActiveRow
     */    
    public function getSensorsName($name)
    {
        return $this->database->table("sensors")->where("name", $name )->fetch();
    }
        

    /**
     * Get count of rows in table
     * @param mixed $name
     * @param string $column DEFAULT = "name"
     * @return int count of rows
     */
    public function getCountSensors($name, $column = "name") :int
    {
        //$result =  $this->database->query('SELECT * FROM sensors WHERE ' . $column . ' = ?', $name);
        //return $result->getRowCount();
        return $this->database->table("sensors")->where($column, $name)->count();
    }

    /**
     * Find sensors with specific name
     * @param string $name
     * @return null|\Nette\Database\Table\ActiveRow
     */    
    public function findSensorsName($name)
    {
        return $this->database->table("sensors")->where("name LIKE ?", "%".$name."%" )->fetchAll();
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
     * @param string $sensorName
     * @return bool
     */    
    public function addThisSensor($sensorName)
    {
        try{
            $this->database->query("CREATE TABLE $sensorName (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                state ENUM('FINISHED','STOP','REWORK', 'ON', 'OFF') NOT NULL DEFAULT 'WORK',
                -- work INT(11) NOT NULL,               
                time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )");
        } catch (Nette\Database\DriverException $e){
            if($e->errorInfo[0]!="42S01") //Skip if error is "table is already exist"
            {
                return false;
            }   
        }
        return true;
    }

    /**
     * Rename sensor table
     * @param string $oldName
     * @param string $sensorName
     * @return bool 
     */ 
    public function renameThisSensor($oldName, $sensorName)
    {

        try{
            $this->database->query("ALTER TABLE $oldName
            RENAME TO $sensorName");    
        } catch (Nette\Database\DriverException $e) {
            //if sensor does no exist add new sensor to DB
            return $this->addThisSensor($sensorName); //
        }
        return true;
    }  
    
    /**
     * Delete sensor table
     * @param string $sensorName
     * @return \Nette\Database\ResultSet
     */     
    public function deleteThisSensor($sensorName)
    {
        return $this->database->query("DROP TABLE $sensorName");
    }

    

    /**
     * Add new sensor
     * @param int $number machine number
     * @param string $name machine name
     * @param string $description machine description (optional)
     * @return array (bool - STATE, string - EN, string - CZ)
     */
    public function addNewSensor($number, $name, $description = "")
    {
        if(ctype_alnum($name)==false)
        {
            return array(false, "", "I can't create a sensor with this name", "Senzor s tímto názvem neumím vytvořit");
        }
        
        if($this->sensorIsExist($number, "number") )
        {
            return array(false, "", "Sensor with this number is exist", "Senzor s tímto číslem již existuje");
        }

        if($this->sensorIsExist($name, "name"))
        {
            return array(false, "", "Sensor with this name is exist", "Senzor s tímto názvem již existuje");
        }

        $res = $this->addThisSensor($name);

        if(!$res)
        {
            return array(false, "", "There is a very serious database error you should contact your administrator!", "Nastala velmi závažná chyba v databázi měli byste kontaktovat svého administrátora!");
        }

        if($succes = $this->database->table("sensors")->insert([
            'number' => $number,
            'name' => $name,
            'description' => $description,
        ]))
        {            
            return array(true, "", "Sensor created", "Senzor byl vytvořen");
        }
        else
        {
            return array(false, "", "ERROR!!!", "ERROR!!!");
        }        
        
    }

    /**
     * Delete sensor
     * @param string $name machine name
     * @return array (bool - STATE, string - EN, string - CZ)
     */
    public function deleteSensor($name)
    {
        if(!$this->sensorIsExist($name, "name") )
        {
            return array(false, "", "The sensor you want to delete does not exist", "Senzor který chceš smazat neexistuje");
        }

        $count = $this->database->table("sensors")
            ->where('name', $name)
            ->delete();

        $this->deleteThisSensor($name);

        return array($count,"", "Sensor deleted", "Senzor byl smazán");
    }  

    /**
     * Edit sensor
     * @param string $oldName machine old name to edit
     * @param int $number machine number
     * @param string $name machine name
     * @param string $description machine description (optional)
     * @return array (bool - STATE, string - EN, string - CZ)
     */
    public function editSensor($oldName, $number, $name, $description = "")
    {
        if(!$this->sensorIsExist($oldName, "name") )
        {
            return array(false, "", "The sensor you want to edit does not exist", "Senzor který chceš upravit neexistuje");
        }        
        
        $oldSen = $this->getSensorsName($oldName);

        if(ctype_alnum($name)==false)
        {
            return array(false, "", "I can't create a sensor with this name", "Senzor s tímto názvem neumím vytvořit");
        }

        //Is not same?
        if(($oldSen->number!=$number)==true)
        {
            //Is exist?
            if($this->sensorIsExist($number, "number") )
            {
                return array(false, "", "Sensor with this number is exist", "Senzor s tímto číslem již existuje");
            }
        }

        //Is not same?
        if(($oldSen->name!=$name)==true)
        {
            if($this->sensorIsExist($name, "name"))
            {
                return array(false, "", "Sensor with this name is exist", "Senzor s tímto názvem již existuje");
            }
            $this->renameThisSensor($oldSen->name, $name);
        }
       

        $result = $this->database->query('UPDATE sensors  SET', [ 
            'number' => $number,
            'name' => $name,
            'description' => $description,
        ], 'WHERE name = ?', $oldName);

        return array($result,"", "Sensor edited", "Senzor byl upraven");
    }
    
    public function getAPILanguage()
	{
		return $this->defaultAPILanguage;
    }
    
    public function getMsgLanguage()
	{
		return $this->defaultMsgLanguage;
	}    
    


}
