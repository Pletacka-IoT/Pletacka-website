<?php

namespace App\CoreModule\Model;

use Nette;
use Nette\Database\Context;
use App\CoreModule\Model\SensorManager;
use DateInterval;
use DateTimeZone;
use Nette\Utils\DateTime;
//use DateTime;
use DateTimeImmutable;

class ThisSensorManager
{
    use Nette\SmartObject;

    
	const
        START = "2000-01-01 00:00:00",
        MINUTE = 60;

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

    public function getRunTimeStamp($sName, $ids): DateInterval
    {
        $last = $this->database->table($sName)->where("id=?", 1)->fetch();
        echo $out =  $last->time->getTimestamp();
        dump($last->time);
        $workTime = 0;

        // $next = $this->database->table($sName)->where("id=?", 2)->fetch();
        // echo $next->time->getTimestamp();
        // dump($next->time);


        // $start = DateTime::from($out);

        // echo $start;


        foreach($ids as $id)
        {
            $actual = $this->database->table($sName)->where("id=?", $id)->fetch();
            
            //dump($last->actWork);

            //  First loop
            if($id < $ids[1])
            {
                $actWork=3*self::MINUTE; // 3 Minutes
                $workTime+=$actWork;
                $lastWork = $actWork;
                $this->database->table($sName)->where("id=?", $id)->update(['work'=>$actWork, 'time'=>$actual->time]);
                echo "FIRST";


            }
            // Next loops
            else if($id >= $ids[1])
            {
                $actWork=$actual->time->getTimestamp()-$last->time->getTimestamp();
                if($actWork>3*self::MINUTE)
                {
                    echo "NEXT - BIG";
                    $actWork = $lastWork;
                    $workTime+=$actWork;
                    
                }
                else
                {
                    $workTime+=$actWork;
                    echo "NEXT - NORMAL";

                }
                $this->database->table($sName)->where("id=?", $id)->update(['work'=>$actWork, 'time'=>$actual->time]);
                             
                $last = $actual;
                $lastWork = $actWork;
                
            }
            echo " -ID:".$id." Last: ".$last->time." Actual: ".$actual->time."<br>";
            echo "Add:".$actWork."->T:".gmdate("H:i:s",$actWork)." -> result: ".$workTime."->T:".gmdate("H:i:s",$workTime)."<br><br><br>";
        }
        $this->database->table($sName)->where("id=?", 10)->update(['work'=>$workTime, 'time'=>$actual->time]);
        return new DateInterval("PT".$workTime."S");
    }

 
    public function dateIntervalToSeconds($dateInterval)
    {
        $reference = new DateTimeImmutable;
        $endTime = $reference->add($dateInterval);

        return $endTime->getTimestamp() - $reference->getTimestamp();
    }
    


    ////////////////////
    // Time counting
    ////////////////////
    public function getRunTime($sName, $ids)
    {
        $start = DateTime::from(self::START);
        $work = DateTime::from("2000-01-01 00:03:08");
        $workTime = date_diff($start, $work);
        echo $start;
        dump($ids);
        //echo "x".$ids[1];
        $last = $this->database->table($sName)->where("id=?", 1)->fetch();
        
        dump($workTime);
        echo "First: ".$workTime->format("%H:%I:%S"); //$last->time ;



        //$workTime = $last->work;
        foreach($ids as $id)
        {
            $actual = $this->database->table($sName)->where("id=?", $id)->fetch();
            
            //dump($last->work);

            //  First loop
            if($id < $ids[1])
            {
                echo "<br><br>LOOP ID:".$id."->".$last->time." - ".$actual->time;
                echo "<br>Add: ".$start->add( $workTime);
                $this->database->table($sName)->where("id=?", $id)->update(['work'=>$workTime->format("%H:%I:%S")]);
                $lastDiff = date_diff(DateTime::from($last->time), DateTime::from($actual->time));//3 Minutes 0 seconds
                
            }
            // Next loops
            else if($id >= $ids[1])
            {
                echo "<br><br><br>LOOP ID:".$id."->".$last->time." - ".$actual->time;                
                $actDiff = date_diff(DateTime::from($last->time), DateTime::from($actual->time));//3 Minutes 0 seconds
                echo "<br>actDiff:";dump($actDiff);
                if(($actDiff->i)>($last->work->i+1))
                {
                    echo"  *BIG: ".$actDiff->i." ->".$start->add( $lastDiff);
                    $this->database->table($sName)->where("id=?", $id)->update(['work'=>$lastDiff->format('%H:%I:%S')]);
                }
                else 
                {
                    echo " *NORMAL*".$actDiff->i." ->".$start->add($actDiff);
                    $this->database->table($sName)->where("id=?", $id)->update(['work'=>$actDiff->format('%H:%I:%S')]);
                    
                }
                //$this->database->table($sName)->where("id=?", $id)->update(['work'=>/*$actDiff->format('%H:%I:%S')*/$workTime]);
                $last = $actual;
                $lastDiff = $actDiff;
            }
            
        }
        $out = date_diff(DateTime::from(self::START),$start);
        return $out;
    }

    public function getRunTime4($sName, $ids)
    {
        $start = DateTime::from(self::START);
        $work = DateTime::from("2000-01-01 00:03:08");
        $workTime = date_diff($start, $work);
        echo $start;
        dump($ids);
        //echo "x".$ids[1];
        $last = $this->database->table($sName)->where("id=?", 1)->fetch();
        
        dump($workTime);
        echo "First: ".$workTime->format("%H:%I:%S"); //$last->time ;



        //$workTime = $last->work;
        foreach($ids as $id)
        {
            $actual = $this->database->table($sName)->where("id=?", $id)->fetch();
            
            //dump($last->work);

            //  First loop
            if($id < $ids[1])
            {
                echo "<br><br>LOOP ID:".$id."->".$last->time." - ".$actual->time;
                echo "<br>Add: ".$start->add( $last->work);
                $this->database->table($sName)->where("id=?", $id)->update(['work'=>$last->work->format("%H:%I:%S")]);
                $lastDiff = date_diff(DateTime::from($last->time), DateTime::from($actual->time));//3 Minutes 0 seconds
                
            }
            // Next loops
            else if($id >= $ids[1])
            {
                echo "<br><br><br>LOOP ID:".$id."->".$last->time." - ".$actual->time;                
                $actDiff = date_diff(DateTime::from($last->time), DateTime::from($actual->time));//3 Minutes 0 seconds
                echo "<br>actDiff:";dump($actDiff);
                if(($actDiff->i)>($last->work->i+1))
                {
                    echo"  *BIG: act:".$actual->work->format('%H:%I:%S')." last:".$last->work->format('%H:%I:%S')." ->".$start->add( $lastDiff);
                    $this->database->table($sName)->where("id=?", $id)->update(['work'=>$lastDiff->format('%H:%I:%S')]);
                }
                else 
                {
                    echo " *NORMAL* act:".$actual->work->format('%H:%I:%S')." last:".$last->work->format('%H:%I:%S')." ->".$start->add($actDiff);
                    $this->database->table($sName)->where("id=?", $id)->update(['work'=>$actDiff->format('%H:%I:%S')]);
                    
                }
                //$this->database->table($sName)->where("id=?", $id)->update(['work'=>/*$actDiff->format('%H:%I:%S')*/$workTime]);
                $last = $actual;
                $lastDiff = $actDiff;
            }
            
        }
        $out = date_diff(DateTime::from(self::START),$start);
        return $out;
    }

    public function getRunTime3($sName, $ids)
    {
        $start = DateTime::from(self::START);
        $work = DateTime::from("2000-01-01 00:03:00");
        $workTime = date_diff($start, $work);
        echo $start;
        dump($ids);
        //echo "x".$ids[1];
        $last = $this->database->table($sName)->where("id=?", 1)->fetch();
        
        dump($workTime);
        echo "First: ".$workTime->format("%H:%I:%S"); //$last->time ;



        //$workTime = $last->work;
        foreach($ids as $id)
        {
            $actual = $this->database->table($sName)->where("id=?", $id)->fetch();
            // echo "Last work";
            // dump($last->work);

            //  First loop
            if($id < $ids[1])
            {
                echo "<br><br>LOOP ID:".$id."->".$last->time." - ".$actual->time;
                echo "<br>Add: ".$start->add( $workTime);
                $this->database->table($sName)->where("id=?", $id)->update(['work'=>$workTime->format("%H:%I:%S")]);
                $lastDiff = date_diff(DateTime::from($last->time), DateTime::from($actual->time));//3 Minutes 0 seconds
                
            }
            // Next loops
            else if($id >= $ids[1])
            {
                echo "<br><br><br>LOOP ID:".$id."->".$last->time." - ".$actual->time;                
                $actDiff = date_diff(DateTime::from($last->time), DateTime::from($actual->time));//3 Minutes 0 seconds
                echo "<br>actDiff:";dump($actDiff);
                if(($actDiff->i)>($last->work->i+1))
                {
                    // if($last->id != $id-1)
                    // {
                        echo "<br>XXXXXXXXXX<br>";
                        echo"  *BIG: ".$last->work->format('%H:%I:%S')." ->".$start->add( $last->work /*$this->timeToInterval($last->work)*/);
                        $this->database->table($sName)->where("id=?", $id)->update(['work'=>$last->work->format('%H:%I:%S')]);
                    // }
                    // else
                    // {
                    //     echo"  *BIG: ".$actDiff->i." ->".$start->add( $lastDiff);
                    //     $this->database->table($sName)->where("id=?", $id)->update(['work'=>$lastDiff->format('%H:%I:%S')]);
                    // }

                }
                else 
                {
                    echo " *NORMAL*".$actDiff->format('%H:%I:%S')." ->".$start->add($actDiff);
                    $this->database->table($sName)->where("id=?", $id)->update(['work'=>$actDiff->format('%H:%I:%S')]);
                    $lastDiff = $actDiff;
                    
                }
                //$this->database->table($sName)->where("id=?", $id)->update(['work'=>/*$actDiff->format('%H:%I:%S')*/$workTime]);
                $last = $actual;
                
            }
            
        }
        $out = date_diff(DateTime::from(self::START),$start);
        return $out;
    }


    public function timeToInterval($work):DateInterval
    {
        $out = explode(":", $work);
        $hour = $this->crop($out[0]);
        $minute = $this->crop($out[1]);
        $second = $this->crop($out[2]);

        $start = DateTime::from("2000-01-01 00:00:00");
        
        $work = DateTime::from("2000-01-01 ".$hour.":".$minute.":".$second);
        $workTime = date_diff($start, $work);
        return $workTime;

    }

    public function crop($input)
    {
        if($input[0]=="0")
        {
            return $input[1];
        }
        return $input;
    }

    //////////////////////////////

    public function getRunTime2($sName, $ids)
    {
        $start = DateTime::from(self::START);
        $work = DateTime::from("2000-01-01 00:03:00");
        $workTime = date_diff($start, $work);
        echo $start;
        dump($ids);
        //echo "x".$ids[1];
        $last = $this->database->table($sName)->where("id=?", $ids[0])->fetch();
        echo "First: ".$last->time ;

        $workTime = $last->work;
        foreach($ids as $id)
        {
            
            
            //dump($last->work);
            if($id < $ids[1])
            {
                echo "<br>Add: ".$start->add( $workTime);
                $this->database->table($sName)->where("id=?", $id)->insert(['work'=>$workTime]);
                
            }
            else if($id >= $ids[1])
            {
                $actual = $this->database->table($sName)->where("id=?", $id)->fetch();
                
                echo "<br>ID:".$id."->";
                
                echo $last->time." - ".$actual->time;
                $add = date_diff(DateTime::from($last->time), DateTime::from($actual->time));
                dump($add);
                if(($add->i)>($actual->work->i))
                {
                    echo"  *BIG: ".$add->i." ->".$start->add( $workTime);
                }
                else if(($add->i)<=($actual->work->i))
                {
                    echo " *NORMAL*".$add->i." ->".$start->add($add);
                    $this->database->table($sName)->where("id=?", $id)->insert(['work'=>$add]);
                }
            
                $last = $actual;
            }
            
        }
        $out = date_diff(DateTime::from(self::START),$start);
        return $out;
    }  
    
    public function resetDB($sName)
    {
        for ($i = 0;$i<=8;$i++ )
        {
            $this->database->table($sName)->where("id = ?", $i)->update([ "work"=>"0"]);
        }

        $this->database->table($sName)->where("id = 1")->update(["time"=>"2020-04-24 22:03:00", "work"=>"0"]);
        $this->database->table($sName)->where("id = 2")->update(["time"=>"2020-04-24 22:06:00"]);
        $this->database->table($sName)->where("id = 3")->update(["time"=>"2020-04-24 22:08:00"]);
        $this->database->table($sName)->where("id = 4")->update(["time"=>"2020-04-24 22:13:00"]);
        $this->database->table($sName)->where("id = 5")->update(["time"=>"2020-04-24 22:15:00"]);
        $this->database->table($sName)->where("id = 6")->update(["time"=>"2020-04-24 22:19:00"]);
        $this->database->table($sName)->where("id = 7")->update(["time"=>"2020-04-24 22:20:59"]);
        $this->database->table($sName)->where("id = 8")->update(["time"=>"2020-04-24 22:22:50"]);

        $this->database->table($sName)->where("id = ?", 10)->update([ "work"=>"0"]);



    }
}


