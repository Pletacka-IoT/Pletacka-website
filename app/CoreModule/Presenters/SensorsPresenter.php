<?php

declare(strict_types=1);

namespace App\CoreModule\Presenters;

use Nette;
use App\CoreModule\Model\SensorsManager;
use App\CoreModule\Model\ThisSensorManager;
use Nette\Application\UI\Form;
use App\CoreModule\Forms\SensorsFormFactory;
use App\CoreModule\Forms\ThisSensorFormFactory;
use Nette\Http\Request;
use Nette\Http\UrlScript;
use App\Presenters\BasePresenter;
use Nette\Utils\Strings;
use Nette\Http\Session;
use Nette\Utils\DateTime;
use App\Exceptions;
use App\Exceptions\MyException;
use Exception;
use Nette\Application\BadRequestException;
use App\TimeManagers\TimeBox;

final class SensorsPresenter extends BasePresenter
{
	const
		FORM_MSG_REQUIRED = 'Tohle pole je povinné.',
        FORM_MSG_RULE = 'Tohle pole má neplatný formát.',
        PLETE = '1',
        STOJI = "0";


        
        
    private $sensorsManager;
    private $request;
    private $urlParameter;
    private $thisSensorManager;
    private $sensorsFormFactory;
    private $thisSensorFormFactory;

	public function __construct(SensorsManager $sensorsManager, ThisSensorManager $thisSensorManager, Request $request,  SensorsFormFactory $sensorsFormFactory, ThisSensorFormFactory $thisSensorFormFactory)
	{
        
        $this->sensorsManager = $sensorsManager;
        $this->thisSensorManager = $thisSensorManager;
        $this->request = $request;
        $this->sensorsFormFactory = $sensorsFormFactory;
        $this->thisSensorFormFactory = $thisSensorFormFactory;
    }

    ///////////////////
    //Pridani senzoru
    ///////////////////
    public function createComponentAddSensorForm(): Form
    {
		return $this->sensorsFormFactory->createCreate(function (Form $form, \stdClass $values) {
            $returnMessage = $this->sensorsManager->addNewSensor($values->number, $values->name, $values->description);
            if($returnMessage[0])
            {
                $this->flashMessage($returnMessage[3], 'success');
                $this->redirect('Sensors:sensor',$values->name);
            }
            else
            {
                
                $this->flashMessage($returnMessage[3], 'error');
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
            $returnMessage = $this->sensorsManager->editSensor($values->oldname, $values->number, $values->name, $values->description);
            if($returnMessage[0])
            {
                $this->flashMessage($returnMessage[3], 'success');
                $this->redirect('Sensors:sensor',$values->name);
            }
            else
            {
                
                $this->flashMessage($returnMessage[3], 'error');
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
            $returnMessage = $this->sensorsManager->deleteSensor($values->name);
            if($returnMessage[0])
            {
                $this->flashMessage($returnMessage[3], 'success');
                $this->redirect('this');
            }
            else
            {
                
                $this->flashMessage($returnMessage[3], 'error');
                $this->redirect('this');
            }
		});        
    }    

    ////////////////////////////////////////////////
    // Default page
    ////////////////////////////////////////////////

    public function renderDefault() : void
    {
        
        $this->template->sensors = $this->sensorsManager->getSensors();

    }



	public function handleReloadSensorTable(): void
	{
		$this->redrawControl('sensorTable');
	}    

    /*
     * Show sensor info
     */
    public function renderSensor($name)
    {
        if(!$this->sensorsManager->sensorIsExist($name))
        {
            $message = array(false, "This sensor does not exist","Tento senzor neexistuje");
            $this->flashMessage($message[2], 'error');
            $this->redirect('Sensors:default');
            
        }

        $this->template->sensor = $this->sensorsManager->getSensorsName($name);
        $this->template->name = $this->sensorsManager->getSensorsName($name)["name"];
//        $this->template->rawEvents = null;
        

    }

    public function createComponentShowForm(): Form
    {
        return $this->thisSensorFormFactory->show(function (Form $form, \stdClass $values) {
            // Get sensor name
            $url = $this->request->getHeaders()["referer"];
            $exUrl = explode('/', $url);
            $exUrl = explode('?', $exUrl[7]);
            $sName = $exUrl[0];

            $from = $values->from;
            $to = $values->to;

            if($from>$to)
            {
                $this->flashMessage("Tento rozsah nelze zobrazit", 'error');
                return;
            }

            $this->template->rawEvents = $rawEvents = $this->thisSensorManager->getAllEvents($sName, $from, $to);

            if($rawEvents)
            {
                $events = new TimeBox($rawEvents);

                $this->template->events = $events->getEvents();
                //
                $this->template->countAll = $events->countEvents();
                $this->template->countFinished = $events->countEvents(TimeBox::FINISHED);
                $this->template->countStop = $events->countEvents(TimeBox::STOP);
                $this->template->countRework = $events->countEvents(TimeBox::REWORK);
                $this->template->countOn = $events->countEvents(TimeBox::ON);
                $this->template->countOff = $events->countEvents(TimeBox::OFF);
                $this->template->allTime = $events->allTime();
                $this->template->stopTime = $events->stopTime();
                $this->template->workTime = $events->workTime();
                $this->template->avgStopTime = $events->avgStopTime();
                $this->template->avgWorkTime = $events->avgWorkTime();
            }


            echo("");
//            $returnMessage = $this->sensorsManager->editSensor($sName,$values->number, $values->name, $values->description);
//            if($returnMessage[0])
//            {
//                $this->flashMessage($returnMessage[3], 'success');
//                $this->redirect('Sensors:sensor',$values->name);
//            }
//            else
//            {
//
//                $this->flashMessage($values->old."*".$returnMessage[3], 'error');
//                $this->redirect('this');
//            }
        });
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
            $sName = $exUrl[0];
            
            
            $returnMessage = $this->sensorsManager->editSensor($sName,$values->number, $values->name, $values->description);
            if($returnMessage[0])
            {
                $this->flashMessage($returnMessage[3], 'success');
                $this->redirect('Sensors:sensor',$values->name);
            }
            else
            {
                
                $this->flashMessage($values->old."*".$returnMessage[3], 'error');
                $this->redirect('this');
            }
		});        
    }    
    

    public function renderEdit($name)
    {

        if(!$this->sensorsManager->sensorIsExist($name))
        {
            $message = array(false, "This sensor does not exist","Tento senzor neexistuje");
            $this->flashMessage($message[2], 'error');
            $this->redirect('Sensors:default');
        }
        $sensor = $this->sensorsManager->getSensorsName($name);
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

        
        

        if(!$this->sensorsManager->sensorIsExist($name))
        {
            $message = array(false,"", "This sensor does not exist","Tento senzor neexistuje");
            $this->flashMessage($message[2], 'error');
            $this->redirect('Sensors:default');
        }

        $returnMessage = $this->sensorsManager->deleteSensor($name);
        if($returnMessage[0])
        {
            $this->flashMessage($returnMessage[3], 'success');
            $this->redirect('Sensors:default');
        }
        else
        {
            
            $this->flashMessage($returnMessage[3], 'error');
            $this->redirect('Sensors:default');
        }

    }



    ////////////////////////////////////////////////
    //  Test Page
    ////////////////////////////////////////////////

    public function renderTest($name, $next)
    {
        // $output = array();
        // $sensors = $this->sensorsManager->getSensors();
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

        // $sensor = $this->sensorsManager->getSensorsNumber(3);
        // dump(array('number'=>$sensor->number, 'name'=>$sensor->name, 'description'=>$sensor->description));

        // dump($this->sensorsManager->findSensorsName("3"));

        
        
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
        // $date1=DateTime::from("2020-04-17 15:52:00");
        // $date2=DateTime::from("2020-04-17 15:53:00");

        // echo date_format($date1,"Y-m/d H:i:s")."<br>";
        // echo date_format($date2,"Y-m/d H:i:s")."<br>";
        // $x  =  date_diff($date2, $date1);
        // // echo date_format($x,"Y-m/d H:i:s")."<br>";          
        // dump($x);
        // echo ($x->format('Y-m-d H:i:s.u'));

        // dump($this->sensorsManager->getSensorInfo("Debuga"));
        dump($this->sensorsManager->getSensorsNumber(121));
        echo "Ahoj";
        // dump($this->sensorsManager->getTitleSettings()[1]->web_nsame);

        // dump($this->sensorsManager->GET)
        // throw new Exception;
        throw new Exceptions\TableNotExist();
        
        // throw new BadRequestException("Bad");
       
    }

}

