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

class ThisSensorManager
{
    use Nette\SmartObject;


	public const
        START = "2000-01-01 00:00:00",
        MINUTE = 60;

    public const
        FINISHED = 'FINISHED',	    // Machine is working
        STOP = "STOP",	    // Machine is not working
        REWORK = "REWORK", 	// State after end of STOP
        ON = 'ON',          // ON machine
        OFF = 'OFF';        // OFF machine



    private $database;
    private $defaultMsgLanguage;
    private $defaultAPILanguage;
    private $sensorsManager;

    public function __construct(Context $database, SensorsManager $sensorsManager)
    {
        $this->database = $database;
        $this->sensorsManager = $sensorsManager;

    }

    public function pretty($state = true, $main, $englishMsg = "", $czechMsg = "" )
    {
        return( array($state, $main, $czechMsg));
        
    }  


    public function testPretty()
    {
         dump($x = Pretty::return(true, array(1,2,3), "IT is ok", "Je to ok"));
//         $y = PrettyReturn::return(true, array(1,2,3), "Je to ok");
        dump(Pretty::return(0,"","Sensor with name  does not exist", "Senzor s názvemneexistuje"));
        // dump($y);
        return 1;//$x;
    }

    

    /**
     * Save sensor status to database
     * @param string $sNumber
     * @param mixed $state
     */
    public function addEvent($sNumber, $state)
    {
        if(!$this->sensorsManager->sensorIsExist($sNumber))
        {
            // return array(false, "Sensor with name ".$sNumber." delete does not exist", "Senzor s názvem".$sNumber." neexistuje");
            return $this->pretty(0,"","Sensor with name ".$sNumber." does not exist", "Senzor s názvem".$sNumber." neexistuje");
        }

        if($succes = $this->database->table("A".$sNumber)->insert([
            'state' => $state,
        ]))
        {
            return $this->pretty(true, "Event created", "Záznam byl vytvořen", $sNumber, $state);
        }
        else
        {
            return $this->pretty(false, "ERROR!!!", "ERROR!!!");
        }
    }
    //BY NAME//

    public function isEmpty($array)
    {
        if(count($array)<1)
        {
            return true;
        }
        return false;
    }


    //////////////
    // Gets
    //////////////

    public function getAllEvents($sNumber, $from="2000-01-01 00:00:00" , $to="2100-01-01 00:00:00")
    {
        return $this->database->table("A".$sNumber)->where("time >=? AND time <=?", $from, $to)->fetchAll();
    }


    /**
     * Reset debug sensor to default values
     * @param $sNumber
     */
    public function resetDB($sNumber)
    {
        for ($i = 0;$i<=8;$i++ )
        {
            $this->database->table("A".$sNumber)->where("id = ?", $i)->update([ "work"=>"0"]);
        }

        $this->database->table("A".$sNumber)->where("id = 1")->update(["time"=>"2020-04-24 22:03:00", "work"=>"0"]);
        $this->database->table("A".$sNumber)->where("id = 2")->update(["time"=>"2020-04-24 22:06:00"]);
        $this->database->table("A".$sNumber)->where("id = 3")->update(["time"=>"2020-04-24 22:08:00"]);
        $this->database->table("A".$sNumber)->where("id = 4")->update(["time"=>"2020-04-24 22:13:00"]);
        $this->database->table("A".$sNumber)->where("id = 5")->update(["time"=>"2020-04-24 22:16:00"]);
        $this->database->table("A".$sNumber)->where("id = 6")->update(["time"=>"2020-04-24 22:19:00"]);
        $this->database->table("A".$sNumber)->where("id = 7")->update(["time"=>"2020-04-24 22:21:00"]);
        $this->database->table("A".$sNumber)->where("id = 8")->update(["time"=>"2020-04-24 22:22:50"]);

        $this->database->table("A".$sNumber)->where("id = ?", 10)->update([ "work"=>"0"]);



    }
}


