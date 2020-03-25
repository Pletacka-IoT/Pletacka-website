<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use App\Model\DatabaseManager;


final class SenPresenter extends Nette\Application\UI\Presenter
{


    private $database;
    
    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }
    


    public function renderDefault() : void
    {
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
    
        
        $this->template->arr = $sensors;
        
    }
    

}
