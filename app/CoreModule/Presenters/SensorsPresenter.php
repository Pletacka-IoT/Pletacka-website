<?php

declare(strict_types=1);

namespace App\CoreModule\Presenters;

use Nette;
use App\CoreModule\Model\SensorsManager;
use App\CoreModule\Model\ThisSensorManager;
use App\CoreModule\Model\ThisChartManager;
use App\CoreModule\Model\ChartManager;
use Nette\Application\UI\Form;
use App\CoreModule\Forms\SensorsFormFactory;
use App\CoreModule\Forms\ThisSensorFormFactory;
use Nette\Http\Request;
use Nette\Http\UrlScript;
use App\Presenters\BasePresenter;
use App\TimeManagers\TimeBox;
use App\Utils\Pretty;

use Jakubandrysek\Chart\DateChart;
use Jakubandrysek\Chart\Serie\DateSerie;
use Jakubandrysek\Chart\Segment\DateSegment;
use DateTimeImmutable;

/**
 * @brief Sensor presenter
 * Show everything about sensor
 */
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
    private $thisChartManager;
    private $chartManager;


	public function __construct(
	    SensorsManager $sensorsManager,
        ThisSensorManager $thisSensorManager,
        Request $request,
        SensorsFormFactory $sensorsFormFactory,
        ThisSensorFormFactory $thisSensorFormFactory,
        ThisChartManager $thisChartManager,
        ChartManager $chartManager
    )
	{
        
        $this->sensorsManager = $sensorsManager;
        $this->thisSensorManager = $thisSensorManager;
        $this->request = $request;
        $this->sensorsFormFactory = $sensorsFormFactory;
        $this->thisSensorFormFactory = $thisSensorFormFactory;
        $this->thisChartManager = $thisChartManager;
        $this->chartManager = $chartManager;
    }

    ///////////////////
    //Pridani senzoru
    ///////////////////
    public function createComponentAddSensorForm(): Form
    {
		return $this->sensorsFormFactory->createCreate(function (Form $form, \stdClass $values) {
            $returnMessage = $this->sensorsManager->addNewSensor($values->number, $values->description);
            if($returnMessage[0])
            {
                $this->flashMessage($returnMessage[2], 'success');
                $this->redirect('Sensors:sensor',$values->number);
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
            $returnMessage = $this->sensorsManager->editSensor($values->oldnumber, $values->number, $values->description);
            if($returnMessage[0])
            {
                $this->flashMessage($returnMessage[2], 'success');
                $this->redirect('Sensors:sensor',$values->number);
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
            $returnMessage = $this->sensorsManager->deleteSensor($values->number);
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
    public function renderInfo($number)
    {
        if(!$this->sensorsManager->sensorIsExist($number))
        {
            $message = Pretty::return(false, "", "Tento senzor neexistuje");
            $this->flashMessage($message[2], 'error');
            $this->redirect('Sensors:default');
            
        }

        // Get sensor name
//        $url = $this->request->getHeaders();
//        $url = explode('?', $url);
//        dump($url);

//        $num = substr($url, -2);

//        $this->flashMessage($url);
//        dump();
        //        $exUrl = explode('?', $exUrl[7]);
        //        $sNumber = $exUrl[0];


        $this->template->sensor = $this->sensorsManager->getSensorsNumber($number);
        $this->template->number = $number;


    }


    //****************

    public function createComponentShowChartForm(): Form
    {
        $form = new Form; // means Nette\Application\UI\Form

        $form->addHidden('time');
//            ->setHtmlId('reportrange')
//            ->setRequired(self::FORM_MSG_REQUIRED);
//            ->setDefaultValue("2020-05-05T05:00")

//        $form->add

        $form->addText('from', 'Do')
            ->setHtmlId('from')
            ->setHtmlType('datetime-local');

        $form->addText('to', 'Do')
            ->setHtmlId('to')
            ->setHtmlType('datetime-local');
//
//        $form->addButton('day', "Den")
//            ->setHtmlAttribute('onclick', 'setDay()');
//
//        $form->addButton('week', "Tyden")
//             ->setHtmlAttribute('onclick', 'setWeek()');
//
//        $form->addButton('month', "Mesic")
//            ->setHtmlAttribute('onclick', 'setMonth()');
//
//        $form->addButton('all', "Vse")
//            ->setHtmlAttribute('onclick', 'setAll()');

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
        $exUrl = explode('?', $exUrl[5]);
        $sNumber = $exUrl[0];

        $this->flashMessage($sNumber);
//        $sNumber = 5;

        $from = $values->from;
        $to = $values->to;

        if($from>$to)
        {
            $this->flashMessage("Tento rozsah nelze zobrazit", 'error');
            return;
        }

        $this->template->rawEvents = $rawEvents = $this->thisSensorManager->getAllEvents($sNumber, $from, $to);


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
            $sNumber = $exUrl[0];

            $from = $values->from;
            $to = $values->to;

            if($from>$to)
            {
                $this->flashMessage("Tento rozsah nelze zobrazit", 'error');
                return;
            }

            $this->template->rawEvents = $rawEvents = $this->thisSensorManager->getAllEvents($sNumber, $from, $to);

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
//            $returnMessage = $this->sensorsManager->editSensor($sNumber,$values->number, $values->number, $values->description);
//            if($returnMessage[0])
//            {
//                $this->flashMessage($returnMessage[2], 'success');
//                $this->redirect('Sensors:sensor',$values->number);
//            }
//            else
//            {
//
//                $this->flashMessage($values->old."*".$returnMessage[2], 'error');
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
            $sNumber = $exUrl[0];

            echo"";
            
            
            $returnMessage = $this->sensorsManager->editSensor($sNumber,$values->number, $values->description);
            if($returnMessage[0])
            {
                $this->flashMessage($returnMessage[2], 'success');
                $this->redirect('Sensors:sensor',$values->number);
            }
            else
            {
                
                $this->flashMessage($returnMessage[2], 'error');
                $this->redirect('this');
            }
		});        
    }    
    

    public function renderEdit($number)
    {

        if(!$this->sensorsManager->sensorIsExist($number))
        {
            $message = Pretty::array(false,"", "This sensor does not exist","Tento senzor neexistuje");
            $this->flashMessage($message[2], 'error');
            $this->redirect('Sensors:default');
        }
        $sensor = $this->sensorsManager->getSensorsNumber($number);
        $this->template->sensor = $sensor;
        $this->template->number = $number;
        $this['editSensorForm']->setDefaults($sensor);

    }

    ////////////////////////////////////////////////
    //  Delete sensor Page
    ////////////////////////////////////////////////

    public function actionDelete($number)
    {
        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Sign:in');
        }        

        
        

        if(!$this->sensorsManager->sensorIsExist($number))
        {
            $message = Pretty::array(false,"", "Tento senzor neexistuje");
            $this->flashMessage($message[2], 'error');
            $this->redirect('Sensors:default');
        }

        $returnMessage = $this->sensorsManager->deleteSensor($number);
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



    public function actionDebug()
    {


        $type = DateSerie::AREA_SPLINE;

        $this->template->rawEvents = $rawEvents = $this->thisSensorManager->getAllEvents('Pletacka1', "2020-05-05 04:01:00", "2020-05-05 14:00:00");

        $interval = 15;

        ($dataChartF = $this->thisChartManager->sensorChartData($rawEvents, 'minute', $interval, 'FINISHED'));
        dump($dataChartF);

        ($dataChartS = $this->thisChartManager->sensorChartData($rawEvents, 'minute', $interval, 'STOP'));

        $dayChart = new DateChart();
        $dayChart->enableTimePrecision(); // Enable time accurate to seconds

        $serie = new DateSerie($type, 'Upleteno - kusů', 'green');
        foreach($dataChartF as $data)
        {
            if($data[0] != 0 || $data[1] != 0)
            {
                $serie->addSegment(new DateSegment(new DateTimeImmutable($data[1]), $data[0]));
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

