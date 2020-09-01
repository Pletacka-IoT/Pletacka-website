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
final class SensorsSettingsPresenter extends BasePresenter
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
                $this->redirect('SensorsSettings:edit',$values->number);
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



    ////////////////////////////////////////////////
    //  Edit page
    ////////////////////////////////////////////////

    public function createComponentEditSensorForm(): Form
    {
		return $this->sensorsFormFactory->createEdit(function (Form $form, \stdClass $values) {
            // Get sensor name
            $url = $this->request->getHeaders()["referer"];
            $exUrl = explode('/', $url);
            $exUrl = explode('?', $exUrl[5]);
            $sNumber = $exUrl[0];
            if(!is_numeric($sNumber))
            {
                $url = $this->request->getHeaders()["referer"];
                $exUrl = explode('/', $url);
                $exUrl = explode('?', $exUrl[7]);
                $sNumber = $exUrl[0];
            }

            echo"";


            $returnMessage = $this->sensorsManager->editSensor($sNumber,$values->number, $values->description);
            if($returnMessage[0])
            {
                $this->flashMessage($returnMessage[2], 'success');
                $this->redirect('this',$values->number);
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
            $message = Pretty::return(false,"", "Tento senzor neexistuje");
            $this->flashMessage($message[2], 'error');
            $this->redirect('Sensors:default');
        }
        $sensor = $this->sensorsManager->getSensorsNumber($number);
        $this->template->sensor = $sensor;
        $this->template->number = $number;
        $this['editSensorForm']->setDefaults($sensor);

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
                $this->redirect('SensorsSettings:default');
            }
            else
            {

                $this->flashMessage($returnMessage[2], 'error');
                $this->redirect('this');
            }
		});
    }


//    ////////////////////////////////////////////////
//    //  Delete sensor Page
//    ////////////////////////////////////////////////

    public function actionDelete($number)
    {

        if(!$this->sensorsManager->sensorIsExist($number))
        {
            $message = Pretty::return(false,"", "Senzor s číslem " . $number . " neexistuje!");
            $this->flashMessage($message[2], 'error');
            $this->redirect('SensorsSettings:default');
        }
        $sensor = $this->sensorsManager->getSensorsNumber($number);
        $this->template->sensor = $sensor;
        $this->template->number = $number;
        $this['deleteSensorForm']->setDefaults($sensor);


//        if (!$this->getUser()->isLoggedIn()) {
//            $this->redirect('Sign:in');
//        }
//
//
//
//
//        if(!$this->sensorsManager->sensorIsExist($number))
//        {
//            $message = Pretty::return(false,"", "Tento senzor neexistuje");
//            $this->flashMessage($message[2], 'error');
//            $this->redirect('Sensors:default');
//        }
//
//        $returnMessage = $this->sensorsManager->deleteSensor($number);
//        if($returnMessage[0])
//        {
//            $this->flashMessage($returnMessage[2], 'success');
//            $this->redirect('Sensors:default');
//        }
//        else
//        {
//
//            $this->flashMessage($returnMessage[2], 'error');
//            $this->redirect('Sensors:default');
//        }

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

