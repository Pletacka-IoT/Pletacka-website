<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use App\Model\DatabaseManager;
use App\Model\ThisSensorManager;
use Nette\Http\Request;

final class SenPresenter extends Nette\Application\UI\Presenter
{


    private $database;
    private $request;
    private $databaseManager;
    private $thisSensorManager;

    
    public function __construct(Nette\Database\Context $database, Nette\Http\Request $request, DatabaseManager $databaseManager, ThisSensorManager $thisSensorManager)
    {
        $this->database = $database;
        $this->request = $request;
        $this->databaseManager = $databaseManager;
        $this->thisSensorManager = $thisSensorManager;
    }


    public function renderDefault() : void
    {
        /*
        $sensors = $this->database->table('sen');

        foreach ($sensors as $sensor) {
            echo $sensor->number;
            echo $sensor->name;
            echo $sensor->description;
            echo "<br>";
        }

        $name = "Pletac1";
        $sensors->where('name = ?', $name);
        
        foreach ($sensors as $sensor) {
            echo $sensor->number;
            echo $sensor->name;
            echo $sensor->description;
            echo "<br>";
        }


        echo "XXX: " . $sensors[0]->name;
        
        

        echo "<br><br><h1>ascdd</h1>";

        //echo $sensors["name"];
        

        $senId = $this->database->table('sen_id');

        foreach ($senId as $sensor) {
            echo $sensor->number;
            echo $sensor->name;
            echo $sensor->description;
            echo "<br>";
        }  
        echo '<br>';
        
        if($senId = $this->database->table('sen_id')->get(1))
        {
            echo $senId->number;
            echo $senId->name;
            echo $senId->description;
        }
        else
        {
            echo "nic";
        }

        //////// Insert
        if(0)
        {
            if($row = $this->database->table("sen_id")->insert([
                'number' => 8,
                'name' => "pletacka4",
            ]))
            {
                echo "<br>Vytvoreno!!!!:".$row->name."<br>";
            }
    
            
        }


        //////// Update
        if(0)
        {
            $count = $this->database->table("sen_id")
                ->where('id', 1)
                ->update([
                    'name' => "Pletacak1"
                ]);
    
            echo "Pocet zmen:" . $count;
        }

        //////// Delete
        if(0)
        {
            $count = $this->database->table("sen_id")
                ->where('number', 15)
                ->delete();
    
            echo "Pocet zmen:" . $count;
        }   
        
        //pocet
        $counter = $this->database->table("sen_id")->where("number", 8);
        echo "<br>Pocet radku:".$counter->count();

        /////////// Settings

        $set = $this->database->table("settings")->where('id',1);
        
        foreach ($set as $s) {
            echo $s->web_name;
            echo $s->title_footer;
            echo "<br>";
            //print_r($set);
        }

        echo $set[1]->web_name;

        echo "<br> ";
        if(("Pletačka"!=$set[1]->web_name)==true)
        {
            echo "roz";
        }
        else
        {
            echo "stej";
        }
        

        //print_r( $this->request->getHeaders());
    
        
        $this->template->arr = $sensors;
        */

        //$this->databaseManager->renameThisSensor("MujSen3", "MujSen4");

        
        echo "<br>Je validni:".ctype_alnum("Ahoj@");

        //$res = $this->databaseManager->addThisSensor("MujSen2");

        
        //Test this sensoru - vutvoreni
        if(0)
        {
            try{
                $res = $this->database->query("CREATE TABLE Seno1 (
                    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    state ENUM('0','1') NOT NULL DEFAULT '0',               
                    time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                    )");
            } catch (Nette\Database\DriverException $e){
                echo "<br>CHybicka";
                echo $e;
                $this->template->e = $e;
                echo "<br>";


                print_r($e->errorInfo);

                echo "<br>Same".($e->errorInfo[0]!="42S0 1");
            }
        }   

        //Test this sensoru - mazani
        if(0)
        {
            try{
                $res = $this->database->query("DROP TABLE SenName2");
            } catch (Nette\Database\DriverException $e){
                echo "<br>CHybicka";
                echo $e;
                $this->template->e = $e;
                echo "<br>";


                print_r($e->errorInfo);

                echo "<br>Same".($e->errorInfo[0]!="42S01");
            }
            if($res)
            {
                echo "<br>OK";
            }
        }    
        

        //Test this sensoru - mazani
        if(0)
        {
            
            $res = $this->database->query("DROP TABLE SenName2");

            if($res)
            {
                echo "<br>OK";
            }
        }         
        
        
            

        
    }
    

}
