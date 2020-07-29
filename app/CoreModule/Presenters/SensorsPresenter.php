<?php

declare(strict_types=1);

namespace App\CoreModule\Presenters;

use Nette;
use App\CoreModule\Model\SensorsManager;
use App\CoreModule\Model\ThisSensorManager;
use App\CoreModule\Model\ChartManager;
use Nette\Application\UI\Form;
use App\CoreModule\Forms\SensorsFormFactory;
use App\CoreModule\Forms\ThisSensorFormFactory;
use Nette\Http\Request;
use Nette\Http\UrlScript;
use App\Presenters\BasePresenter;
use App\TimeManagers\TimeBox;

use Jakubandrysek\Chart\DateChart;
use Jakubandrysek\Chart\Serie\DateSerie;
use Jakubandrysek\Chart\Segment\DateSegment;
use DateTimeImmutable;

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
    private $chartManager;


	public function __construct(
	    SensorsManager $sensorsManager,
        ThisSensorManager $thisSensorManager,
        Request $request,
        SensorsFormFactory $sensorsFormFactory,
        ThisSensorFormFactory $thisSensorFormFactory,
        ChartManager $chartManager
    )
	{
        
        $this->sensorsManager = $sensorsManager;
        $this->thisSensorManager = $thisSensorManager;
        $this->request = $request;
        $this->sensorsFormFactory = $sensorsFormFactory;
        $this->thisSensorFormFactory = $thisSensorFormFactory;
        $this->chartManager = $chartManager;
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


    //****************

    public function createComponentShowChartForm(): Form
    {
        $form = new Form; // means Nette\Application\UI\Form

        $form->addText('from', 'Od')
            ->setHtmlId('from')
            ->setRequired(self::FORM_MSG_REQUIRED)
            ->setDefaultValue("2020-05-05T05:00")
            ->setHtmlType('datetime-local');

        $form->addText('to', 'Do')
            ->setHtmlId('to')
            ->setRequired(self::FORM_MSG_REQUIRED)
            ->setDefaultValue("2020-05-05T23:43")
            ->setHtmlType('datetime-local');

        $form->addButton('day', "Den")
            ->setHtmlAttribute('onclick', 'setDay()');

        $form->addButton('week', "Tyden")
            ->setHtmlAttribute('onclick', 'setWeek()');

        $form->addButton('month', "Mesic")
            ->setHtmlAttribute('onclick', 'setMonth()');

        $form->addSubmit('send', 'Zobraz')
            ->setHtmlId('send');
        $form->onSuccess[] = [$this, 'ShowChart'];

        return $form;
    }

    public function ShowChart(Form $form, \stdClass $values): void
    {
        $this->flashMessage($values->from."->".$values->to);

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
    }
    //****************








    public function customShowChart(\stdClass $values)
    {
;

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
        $type = DateSerie::AREA_SPLINE;

        $this->template->rawEvents = $rawEvents = $this->thisSensorManager->getAllEvents('Pletacka1', "2020-05-05 00:00:00", "2020-05-05 14:00:00");

        ($dataChartF = $this->chartManager->sensorChartData($rawEvents, 'minute', 15, 'FINISHED'));


        ($dataChartS = $this->chartManager->sensorChartData($rawEvents, 'minute', 15, 'STOP'));

        $dayChart = new DateChart();
        $dayChart->enableTimePrecision(); // Enable time accurate to seconds

        $serie = new DateSerie($type, 'Upleteno - párů', 'green');
        foreach($dataChartF as $data)
        {
            if($data[0] != 0 || $data[1] != 0)
            {
            $serie->addSegment(new DateSegment(new DateTimeImmutable($data[1]), intval($data[0])));
            }
        }
        $dayChart->addSerie($serie);

        $serie = new DateSerie($type, 'Zastaveno - počet', 'red');
        foreach($dataChartS as $data)
        {
            $serie->addSegment(new DateSegment(new DateTimeImmutable($data[1]), $data[0]));
        }

//        $serie->addSegment(new DateSegment(new DateTimeImmutable('2012-02-01'), 4));
//        $serie->addSegment(new DateSegment(new DateTimeImmutable('2012-03-01'), 8));
        $dayChart->addSerie($serie);

        $this->template->dayChart = $dayChart;
       
    }

}

