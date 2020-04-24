<?php

declare(strict_types=1);

namespace App\CoreModule\Presenters;

use App\CoreModule\Forms\SensorsFormFactory;
use Nette;
use App\CoreModule\Model\SensorManager;
use App\CoreModule\Model\ThisSensorManager;
use Nette\Application\UI\Form;
use Nette\Http\Request;
use Nette\Http\UrlScript;
use App\Presenters\BasePresenter;
use Nette\Utils\Strings;
use Nette\Http\Session;
use Nette\Utils\DateTime;

final class SensorsPresenter extends BasePresenter
{
	const
		FORM_MSG_REQUIRED = 'Tohle pole je povinné.',
        FORM_MSG_RULE = 'Tohle pole má neplatný formát.',
        PLETE = '1',
        STOJI = "0";


        
        
    private $sensorManager;
    private $request;
    private $urlParameter;
    private $sensorsFormFactory;
    private $thisSensorManager;

	public function __construct(SensorManager $sensorManager, ThisSensorManager $thisSensorManager, Request $request,  SensorsFormFactory $sensorsFormFactory)
	{
        
        $this->sensorManager = $sensorManager;
        $this->thisSensorManager = $thisSensorManager;
        $this->request = $request;
        $this->sensorsFormFactory = $sensorsFormFactory;
    }

    ///////////////////
    //Pridani senzoru
    ///////////////////
    public function createComponentAddSensorForm(): Form
    {
		return $this->sensorsFormFactory->createCreate(function (Form $form, \stdClass $values) {
            $returnMessage = $this->sensorManager->addNewSensor($values->number, $values->name, $values->description);
            if($returnMessage[0])
            {
                $this->flashMessage($returnMessage[2], 'success');
                $this->redirect('Sensors:sensor',$values->name);
            }
            else
            {
                
                $this->flashMessage($returnMessage[2], 'error');
                $this->redirect('this');
            }  
		});        
    }

    ///////////////////
    //Editace senzoru
    /////////////////// 
    public function createComponentEditSensorxForm(): Form
    {
		return $this->sensorsFormFactory->createEditOld(function (Form $form, \stdClass $values) {
            $returnMessage = $this->sensorManager->editSensor($values->oldname, $values->number, $values->name, $values->description);
            if($returnMessage[0])
            {
                $this->flashMessage($returnMessage[2], 'success');
                $this->redirect('Sensors:sensor',$values->name);
            }
            else
            {
                
                $this->flashMessage($returnMessage[2], 'error');
                $this->redirect('this');
            } 
		});        
    }

    ///////////////////
    //Smazani senzoru
    ///////////////////  
    public function createComponentDeleteSensorForm(): Form
    {
		return $this->sensorsFormFactory->createDelete(function (Form $form, \stdClass $values) {
            $returnMessage = $this->sensorManager->deleteSensor($values->name);
            if($returnMessage[0])
            {
                $this->flashMessage($returnMessage[2], 'success');
                $this->redirect('this');
            }
            else
            {
                
                $this->flashMessage($returnMessage[2], 'error');
                $this->redirect('this');
            }
		});        
    }    

    ////////////////////////////////////////////////
    //  Default page
    ////////////////////////////////////////////////

    public function renderDefault() : void
    {
        
        $this->template->sensors = $this->sensorManager->getSensors();

    }



	public function handleReloadSensorTable(): void
	{
		$this->redrawControl('sensorTable');
	}    


    public function renderSensor($name)
    {
        
        
        if(!$this->sensorManager->sensorIsExist($name))
        {
            $message = array(false, "This sensor does not exist","Tento senzor neexistuje");
            $this->flashMessage($message[2], 'error');
            $this->redirect('Sensors:default');
            
        }

        $this->template->sensor = $this->sensorManager->getSensorsName($name);
        $this->template->name = $this->sensorManager->getSensorsName($name)["name"];
        

    }

    ////////////////////////////////////////////////
    //  Edit page
    ////////////////////////////////////////////////

    public function createComponentEditSensorForm(): Form
    {
		return $this->sensorsFormFactory->createEdit(function (Form $form, \stdClass $values) {
            $url = $this->request->getHeaders()["referer"];
            $exUrl = explode('/', $url);
            $exUrl = explode('?', $exUrl[7]);
            $oldSensor = $exUrl[0];
            
            
            $returnMessage = $this->sensorManager->editSensor($oldSensor,$values->number, $values->name, $values->description);
            if($returnMessage[0])
            {
                $this->flashMessage($returnMessage[2], 'success');
                $this->redirect('Sensors:sensor',$values->name);
            }
            else
            {
                
                $this->flashMessage($values->old."*".$returnMessage[2], 'error');
                $this->redirect('this');
            }
		});        
    }    
    

    public function renderEdit($name)
    {

        if(!$this->sensorManager->sensorIsExist($name))
        {
            $message = array(false, "This sensor does not exist","Tento senzor neexistuje");
            $this->flashMessage($message[2], 'error');
            $this->redirect('Sensors:default');
        }
        $sensor = $this->sensorManager->getSensorsName($name);
        $this->template->sensor = $sensor;
        $this->template->name = $sensor["name"];
        $this['editSensorForm']->setDefaults($sensor);

    }

    ////////////////////////////////////////////////
    //  Delete sensor Page
    ////////////////////////////////////////////////

    public function actionDelete($name)
    {
        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Sign:in');
        }        

        
        

        if(!$this->sensorManager->sensorIsExist($name))
        {
            $message = array(false, "This sensor does not exist","Tento senzor neexistuje");
            $this->flashMessage($message[2], 'error');
            $this->redirect('Sensors:default');
        }

        $returnMessage = $this->sensorManager->deleteSensor($name);
        if($returnMessage[0])
        {
            $this->flashMessage($returnMessage[2], 'success');
            $this->redirect('Sensors:default');
        }
        else
        {
            
            $this->flashMessage($returnMessage[2], 'error');
            $this->redirect('Sensors:default');
        }



        
    }



    ////////////////////////////////////////////////
    //  Test Page
    ////////////////////////////////////////////////

    public function renderTest($name, $next)
    {
        // $output = array();
        // $sensors = $this->sensorManager->getSensors();
        // foreach($sensors as $sensor)
        // {
        //     dump($sensor);
        //     echo($sensor->name);
        //     $output[] = array('number'=>$sensor->number, 'name'=>$sensor->name, 'description'=>$sensor->description);
        // }
        // $xout = array();
        // $xout = array('sensors'=>$output);
        // $xout['status'] = 'kuba';
        // dump($xout);
        // echo json_encode($xout);
        // dump( $xout['sensors']);

        // $sensor = $this->sensorManager->getSensorsNumber(3);
        // dump(array('number'=>$sensor->number, 'name'=>$sensor->name, 'description'=>$sensor->description));

        // dump($this->sensorManager->findSensorsName("3"));

        
        
        //dump($this->thisSensorManager->addEvent("Sen3", self::STOJI));
        

        echo $this->thisSensorManager->countAllEventsState("Sen3", "1")."<br>";
        
        echo "XXX:".$this->thisSensorManager->getAllEventsOlder("Sen3", '2020-04-17 15:56:30')."<br>";
        echo "Time<br>";
        foreach($this->thisSensorManager->getAllEventsYounger("Sen3", '2020-04-17 15:52:30') as $event)
        {
            echo $event->id."->". $event->state."->".$event->time."<br>";
        }

        echo "All<br>";
        foreach($this->thisSensorManager->getAllEvents("Sen3") as $event)
        {
            echo $event->id."->". $event->state."->".$event->time."<br>";
        }
        // date

        
    }

    public function actionDebug()
    {
        $date1=DateTime::from("2020-04-17 15:52:00");
        $date2=DateTime::from("2020-04-17 15:53:00");

        echo date_format($date1,"Y-m/d H:i:s")."<br>";
        echo date_format($date2,"Y-m/d H:i:s")."<br>";
        $x  =  date_diff($date2, $date1);
        // echo date_format($x,"Y-m/d H:i:s")."<br>";          
        dump($x);
        echo ($x->format('Y-m-d H:i:s.u'));
       
    }

}