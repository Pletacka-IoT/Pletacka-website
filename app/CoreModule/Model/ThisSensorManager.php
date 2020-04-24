<?php

namespace App\CoreModule\Model;

use Nette;
use Nette\Database\Context;
use App\CoreModule\Model\SensorManager;
use Nette\Utils\DateTime;
//use DateTime;

class ThisSensorManager
{
    use Nette\SmartObject;

		
	const
        START = "2000-01-01 00:00:00";

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
     * @param string $sName
     * @param mixed $state
     */
    public function addEvent($sName, $state)
    {
        if(!$this->sensorManager->sensorIsExist($sName))
        {
            return array(false, "Sensor with name ".$sName." delete does not exist", "Senzor s názvem".$sName." neexistuje");
        }

        if($succes = $this->database->table($sName)->insert([
            'state' => $state,
        ]))
        {            
            return array(true, "Event created", "Záznam byl vytvořen", $sName, $state);
        }
        else
        {
            return array(false, "ERROR!!!", "ERROR!!!");
        }
    }
    //BY NAME//


    //////////////
    // Gets 
    //////////////

    public function getAllEvents($sName)
    {
        return $this->database->table($sName)->fetchAll();
    }

    public function getAllEventsState($sName, $state)
    {
        return $this->database->table($sName)->where("state", $state)->fetchAll();
    }   
    
    public function getAllEventsOlder($sName, $time)
    {
        return $this->database->table($sName)->where("time >=?", $time)->fetchAll();
    } 

    public function getAllEventsYounger($sName, $time)
    {
        return $this->database->table($sName)->where("time <=?", $time)->fetchAll();
    }
    
    //////////////
    // Counts
    //////////////

    public function countAllEvents($sName)
    {
        return $this->database->table($sName)->count();
    } 
    
    public function countAllEventsState($sName, $state)
    {
        return $this->database->table($sName)->where("state", $state)->count();
    }  

    

    //////////////
    // ID
    //////////////

    public function getFirstId($sName, $time, $state)
    {
        return $this->database->table($sName)->where("time >=? AND state=?", $time, $state)->fetch()->id;
    }    

    public function getLastId($sName, $time, $state)
    {
        return $this->database->table($sName)->where("time <=? AND state=?", $time, $state)->order("id DESC")->fetch()->id;
    }

    public function getAllId($sName, $from , $to, $state)
    {
        $ids = $this->database->table($sName)->where("time >=? AND time <=? AND state=?", $from, $to, $state)->fetchAll();
        
        $out = array();
        foreach($ids as $id)
        {
            $out[] = $id->id;
        }
        return $out;
    }


    ////////////////////
    // Time counting
    ////////////////////

    public function getRunTime($sName, $ids)
    {
        $start = DateTime::from(self::START);
        $work = DateTime::from("2000-01-01 00:03:00");
        $workTime = date_diff($start, $work);
        echo $start;
        dump($ids);
        //echo "x".$ids[1];
        
        $timeLast = DateTime::from($this->database->table($sName)->where("id=?", $ids[0])->fetch()->time);
        foreach($ids as $id)
        {
            
            if($id <= $ids[1])
            {
                $start->add( $workTime);
            }
            else if($id >= $ids[1])
            {
                $actual = $this->database->table($sName)->where("id=?", $id)->fetch();
                
                echo "<br>ID:".$id."->";
                $timeAct = DateTime::from($actual->time);
                
                echo $timeLast." - ".$timeAct;
                $add = date_diff(DateTime::from($timeLast), DateTime::from($timeAct));
                if($add->i>$actual->work)
                {
                    echo"*BIG*".$start->add( $workTime);
                }
                else
                {
                    echo "<br>".$add->i."-S:".$start->add($add);
                }
                

                $timeLast = $timeAct;
            }
            
        }
    }
}


