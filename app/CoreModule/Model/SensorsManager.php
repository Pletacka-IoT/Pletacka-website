<?php

namespace App\CoreModule\Model;


use Exception;
use Nette;
use Nette\Database\Context;
use App\Utils\Pretty;
// use App\CoreModule\Exceptions;
use App\Exceptions;
use App\Exceptions\MyException;

/**
 * @brief Manage sensor with name
 */
class SensorsManager
{
	use Nette\SmartObject;

    public const
        HOUR = "H",
        DAY = "D",
        MONTH = "M",
        YEAR = "Y";

    private $database;
    
    public function __construct( Context $database )
    {
        $this->database = $database;
    }


    /**
     * @brief Get all settings from database
     * @return Exception|\Nette\Database\Table\ActiveRow
     * @throws Exceptions\SettingsNotExist
     */
    public function getTitleSettings()
    {
        try {
            $ret = $this->database->table("settings")->get(1); //number is ID in table settings
        } catch (Nette\InvalidArgumentException $e) {
            throw new Exceptions\SettingsNotExist;
        }
        return $ret;
    }


    
    /**
     * @brief Get all sensors from database
     * @return Nette\Database\Table\Selection
     */    
    public function getSensors()
    {
        return $this->database->table("sensors");
    }

    /**
     * @brief Get sensor with specific number
     * @param int $number machine number
     * @return Exception|\Nette\Database\Table\ActiveRow
     * @throws Exceptions\SensorNotExist
     */
    public function getSensorsNumber(int $number)
    {
        if(($out = $this->database->table("sensors")->where("number", $number )->fetch())==null)
        {
            throw new Exceptions\SensorNotExist;
        }
        return $out;
    }


    /**
     * @brief Get count of rows in table
     * @param $number machine number
     * @return int count of rows
     */
    public function getCountSensors($number) :int
    {
        return $this->database->table("sensors")->where("number = ?", $number)->count();

    }


    /**
     * @brief Is sensor exist?
     * @param $number machine number
     * @return bool
     */
    public function sensorIsExist($number) :bool
    {
        return $this->getCountSensors($number);

    }




    /**
     * ********************************************************************************************
     */




    /**
     * @brief Add new sensor
     * @param int $sensorNumber machine number
     * @return bool create status
     */    
    public function addThisSensor($sensorNumber)
    {
        $sensorNumber = "A".$sensorNumber;
        
        $this->database->query("
            DROP TABLE IF EXISTS `A1-H`;            
            CREATE TABLE $sensorNumber (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            state ENUM('FINISHED','STOP','REWORK', 'ON', 'OFF') NOT NULL DEFAULT 'FINISHED',
            time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )");
        return true;
    }


    /**
     * @brief Add new sensor
     * @param int $sensorNumber machine number
     * @param $selection
     * @return bool create status
     */
    public function addThisSensorSelection($sensorNumber, $selection)
    {
        $sensorNumber = "A".$sensorNumber."_".$selection;

        $this->database->query("
            DROP TABLE IF EXISTS $sensorNumber;
            CREATE TABLE $sensorNumber (
              id int(11) NOT NULL AUTO_INCREMENT,
              time datetime NOT NULL,
              t_stop int(11) NOT NULL DEFAULT 0,
              t_work int(11) NOT NULL DEFAULT 0,
              c_FINISHED int(11) NOT NULL DEFAULT 0,
              c_STOP int(11) NOT NULL DEFAULT 0,
              PRIMARY KEY (id)
            )
        ");
        return true;
    }

    /**
     * @brief Rename sensor table
     * @param int $oldNumber ols machine number
     * @param int $newNumber new machine number
     * @return bool rename status
     */
    public function renameThisSensor(int $oldNumber, int $newNumber)
    {
        $oldNumber = "A".$oldNumber;
        $newNumber = "A".$newNumber;
        try{
            $this->database->query("ALTER TABLE $oldNumber
            RENAME TO $newNumber");
        } catch (Nette\Database\DriverException $e) {
            //if sensor does no exist add new sensor to DB
            return $this->addThisSensor($newNumber); //
        }
        return true;
    }


    /**
     * @brief Rename sensor table
     * @param int $newNumber new machine number
     * @param int $oldNumber ols machine number
     * @param string $selection
     * @return bool rename status
     */
    public function renameThisSensorSelection(int $oldNumber, int $newNumber, string $selection)
    {
        $oldNumber = "A".$oldNumber."_".$selection;
        $newNumber = "A".$newNumber."_".$selection;
        try{
            $this->database->query("ALTER TABLE $oldNumber
            RENAME TO $newNumber");
        } catch (Nette\Database\DriverException $e) {
            //if sensor does no exist add new sensor to DB
            return $this->addThisSensorSelection($newNumber, $selection); //
        }
        return true;
    }
    
    /**
     * @brief Delete sensor table
     * @param int $sensorNumber machine number
     * @return \Nette\Database\ResultSet
     */     
    public function deleteThisSensor($sensorNumber)
    {
        if($this->sensorIsExist($sensorNumber))
        {
            $sensorNumber = "A".$sensorNumber;
            return $this->database->query("DROP TABLE $sensorNumber");
        }
    }

    /**
     * @brief Delete sensor table
     * @param int $sensorNumber machine number
     * @return \Nette\Database\ResultSet
     */
    public function deleteThisSensorSelection($sensorNumber, $selection) :\Nette\Database\ResultSet
    {
        if($this->sensorIsExist($sensorNumber)) {
            $sensorNumber = "A" . $sensorNumber . "_" . $selection;
            return $this->database->query("DROP TABLE $sensorNumber");
        }
    }



    /**
     **********************************************************************
     */



    /**
     * @brief Add new sensor
     * @param int $number machine number
     * @param string $description machine description (optional)
     * @return Pretty pretty output
     */
    public function addNewSensor($number, $description = "")
    {

        if($this->sensorIsExist($number) )
        {
            return new Pretty(false, "" , "Senzor s tímto číslem již existuje");
        }

        $this->addThisSensor($number);
        $this->addThisSensorSelection($number, self::HOUR);
        $this->addThisSensorSelection($number, self::DAY);
        $this->addThisSensorSelection($number, self::MONTH);
        $this->addThisSensorSelection($number, self::YEAR);


        if($success = $this->database->table("sensors")->insert([
            'number' => $number,
            'description' => $description,
        ]))
        {
            return new Pretty(true, "" ,"Senzor byl vytvořen");
        }
        else
        {
            return new Pretty(false, "" , "ERROR!!!");
        }        
        
    }

    /**
     * @brief Delete sensor
     * @param $number machine number
     * @return Pretty pretty output
     */
    public function deleteSensor($number)
    {
        if(!$this->sensorIsExist($number) )
        {
            return new Pretty(false, "" , "Senzor který chceš smazat neexistuje");
        }

        $count = $this->database->table("sensors")
            ->where('number', $number)
            ->delete();

        $this->deleteThisSensor($number);
        $this->deleteThisSensorSelection($number, self::HOUR);
        $this->deleteThisSensorSelection($number, self::DAY);
        $this->deleteThisSensorSelection($number, self::MONTH);
        $this->deleteThisSensorSelection($number, self::YEAR);

        return new Pretty(true, $count , "Senzor byl smazán");
    }

	/**
	 * @brief Edit sensor
	 * @param int $oldNumber machine old number
	 * @param int $number machine number
	 * @param string $description machine description (optional)
	 * @return Pretty pretty output
	 */
    public function editSensor(int $oldNumber, int $number, string $description = "") :Pretty
    {
        if($oldNumber != $number)
        {
            if($this->sensorIsExist($number))
            {
                return new Pretty(false, "" , "Senzor s tímto číslem již existuje");
            }

        }

        $this->renameThisSensor($oldNumber, $number);
        $this->renameThisSensorSelection($oldNumber, $number, self::HOUR);
        $this->renameThisSensorSelection($oldNumber, $number, self::DAY);
        $this->renameThisSensorSelection($oldNumber, $number, self::MONTH);
        $this->renameThisSensorSelection($oldNumber, $number, self::YEAR);


        $result = $this->database->query('UPDATE sensors  SET', [ 
            'number' => $number,
            'description' => $description,
        ], 'WHERE number = ?', $oldNumber);

        return new Pretty(true, $result , "Senzor byl upraven");
    }
    


}
