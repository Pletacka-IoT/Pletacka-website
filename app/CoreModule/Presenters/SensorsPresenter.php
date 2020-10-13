<?php

declare(strict_types=1);

namespace App\CoreModule\Presenters;

use Nette;
use App\CoreModule\Model\SensorsManager;
use App\CoreModule\Model\ThisSensorManager;
use App\CoreModule\Model\ThisChartManager;
use App\CoreModule\Model\ChartManager;
use App\CoreModule\Model\WorkShiftManager;
use App\CoreModule\Forms\SensorsFormFactory;
use App\CoreModule\Forms\ThisSensorFormFactory;
use App\CoreModule\Controls\PletackaChartControl\PletackaChartControlFactory;
use Nette\Http\Request;
use Nette\Application\UI\Form;
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
    private $workShiftManager;
    private $pletackaChartControlFactory;

    private $sNumber;


	public function __construct(
	    SensorsManager $sensorsManager,
        ThisSensorManager $thisSensorManager,
        Request $request,
        SensorsFormFactory $sensorsFormFactory,
        ThisSensorFormFactory $thisSensorFormFactory,
        ThisChartManager $thisChartManager,
        ChartManager $chartManager,
        WorkShiftManager $workShiftManager,
        PletackaChartControlFactory $pletackaChartControlFactory
    )
	{
        
        $this->sensorsManager = $sensorsManager;
        $this->thisSensorManager = $thisSensorManager;
        $this->request = $request;
        $this->sensorsFormFactory = $sensorsFormFactory;
        $this->thisSensorFormFactory = $thisSensorFormFactory;
        $this->thisChartManager = $thisChartManager;
        $this->chartManager = $chartManager;
        $this->workShiftManager = $workShiftManager;
        $this->pletackaChartControlFactory = $pletackaChartControlFactory;
    }

    protected function createComponentPletackaChart()
    {
        return $this->pletackaChartControlFactory->create($this->sNumber);
    }


    public function renderDefault($number)
    {

        if(!$this->sensorsManager->sensorIsExist($number))
        {
            $this->flashMessage("Senzor s číslem " . $number . " neexistuje!", 'error');
            $this->redirect('Homepage:default');
            
        }

        //Setup component number
        $this->sNumber = $number;


//        $this->template->rawEvents = $rawEvents = $this->thisSensorManager->getAllEvents($number, "2020-05-05 06:00:00", "2020-05-05 23:00:00");
        $this->template->sensor = $this->sensorsManager->getSensorsNumber(intval($number));
        $this->template->number = $number;

        $this->template->workShift = $this->workShiftManager->getWeekWS();

        $this['showChartForm']->setDefaults(array("num"=>$number));






//        $from = "2020-04-24 22:01:00";
//        $to = "2020-04-24 23:00:00";
//
//        $this->template->rawEvents = $rawEvents = $this->thisSensorManager->getAllEvents($number, $from, $to);
//        $previousEvent = $this->thisSensorManager->getPreviousEvent($number, $rawEvents);
//        if($previousEvent){$previousEvent = $previousEvent->state;}
//        //        $this->template->rawEvents = $rawEvents = $this->thisSensorManager->getAllEvents($sNumber, "2020-05-05 06:00:00", "2020-05-05 23:00:00");
//
//        if($rawEvents)
//        {
//            $events = new TimeBox($rawEvents, $from, $to);
//
//            $this->template->events = $events->getEvents();
//
//            $this->template->countAll = $events->countEvents();
//            $this->template->countFinished = $events->countEvents(TimeBox::FINISHED);
//            $this->template->countStop = $events->countEvents(TimeBox::STOP);
//            $this->template->countRework = $events->countEvents(TimeBox::REWORK);
//            $this->template->countOn = $events->countEvents(TimeBox::ON);
//            $this->template->countOff = $events->countEvents(TimeBox::OFF);
//            $this->template->allTime = $events->allTime($previousEvent);
//            $this->template->stopTime = $events->stopTime($previousEvent);
//            $this->template->workTime = $events->workTime($previousEvent);
//            $this->template->avgStopTime = $events->avgStopTime($previousEvent);
//            $this->template->avgWorkTime = $events->avgWorkTime($previousEvent);
//        }



    }


    //****************

    public function createComponentShowChartForm(): Form
    {
        $form = new Form; // means Nette\Application\UI\Form


        $form->addHidden("num");

        $form->addButton("choose");


        $form->addHidden('from');

        $form->addHidden('to');

        $form->addSubmit('send', 'Zobraz')
            ->setHtmlId('send');
        $form->onSuccess[] = [$this, 'ShowChart'];

        return $form;

    }

    public function ShowChart(Form $form, \stdClass $values): void
    {
        $this->flashMessage($values->from."->".$values->to);

        $sNumber = $values->num;

        $this->flashMessage($sNumber);


        $from = $values->from;
        $to = $values->to;

        if($from>$to)
        {
            $this->flashMessage("Tento rozsah nelze zobrazit", 'error');
            return;
        }

        $this->template->rawEvents = $rawEvents = $this->thisSensorManager->getAllEvents($sNumber, $from, $to);
//        $this->template->rawEvents = $rawEvents = $this->thisSensorManager->getAllEvents($sNumber, "2020-05-05 06:00:00", "2020-05-05 23:00:00");

        if($rawEvents)
        {
            $events = new TimeBox($rawEvents, 0, 24);

            $this->template->events = $events->getEvents();

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


}

