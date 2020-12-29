<?php

declare(strict_types=1);

namespace App\CoreModule\Presenters;

use App\CoreModule\Model\DatabaseSelectionManager;
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
use Nette\Utils\DateTime;

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
	/**
	 * @var DatabaseSelectionManager
	 */
	private $databaseSelectionManager;


	public function __construct(
	    SensorsManager $sensorsManager,
        ThisSensorManager $thisSensorManager,
        Request $request,
        SensorsFormFactory $sensorsFormFactory,
        ThisSensorFormFactory $thisSensorFormFactory,
        ThisChartManager $thisChartManager,
        ChartManager $chartManager,
		DatabaseSelectionManager $databaseSelectionManager
    )
	{
        
        $this->sensorsManager = $sensorsManager;
        $this->thisSensorManager = $thisSensorManager;
        $this->request = $request;
        $this->sensorsFormFactory = $sensorsFormFactory;
        $this->thisSensorFormFactory = $thisSensorFormFactory;
        $this->thisChartManager = $thisChartManager;
        $this->chartManager = $chartManager;
		$this->databaseSelectionManager = $databaseSelectionManager;
	}

    ///////////////////
    //Pridani senzoru
    ///////////////////
    public function createComponentAddSensorForm(): Form
    {
		return $this->sensorsFormFactory->createCreate(function (Form $form, \stdClass $values) {
            $returnMessage = $this->sensorsManager->addNewSensor($values->number, $values->description);
            if($returnMessage->state)
            {
                $this->flashMessage($returnMessage->msg, 'success');
                $this->redirect('SensorsSettings:edit',$values->number);
            }
            else
            {
                
                $this->flashMessage($returnMessage->msg, 'error');
                $this->redirect('this');
            }  
		});        
    }


	public function createComponentUpdateMultiSensorsDataForm(): Form
	{
		return $this->sensorsFormFactory->createUpdateData(function (Form $form, \stdClass $values) {

			$sensors = $this->sensorsManager->getSensors();
			$returnMessage = $this->databaseSelectionManager->createMultiSelectionForSensorsFromTo($sensors, new DateTime($values->from), new DateTime($values->to));
			if($returnMessage->state)
			{
				$this->flashMessage($returnMessage->msg, 'success');
				$this->redirect('this');
			}
			else
			{

				$this->flashMessage($returnMessage->msg, 'error');
				$this->redirect('this');
			}
		});
	}


	public function createComponentAddFromToForm(): Form
	{
		return $this->sensorsFormFactory->createCreateFromTo(function (Form $form, \stdClass $values) {
			if($values->from<=$values->to)
			{
				$countOK = 0;
				$countError = 0;
				for($i = $values->from; $i<=$values->to; $i++)
				{
					$returnMessage = $this->sensorsManager->addNewSensor($i, $values->description." - ".$i);
					if($returnMessage->state)
					{
						$countOK++;
					}
					else
					{
						$countError++;

					}

				}
				$this->flashMessage("Vytvořeno - ".$countOK, 'success');
				if($countError>0)
				{
					$this->flashMessage("Nevytvořeno - ".$countError, 'error');
				}
				$this->redirect('SensorsSettings:default');
			}
			else
			{
				$this->flashMessage("Neplatné zadání rozmezí", 'success');
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

            $returnMessage = $this->sensorsManager->editSensor(intval($values->oldNumber),intval($values->number), $values->description);
            if($returnMessage->state)
            {
                $this->flashMessage($returnMessage->msg, 'success');
                $this->redirect('this',$values->number);
            }
            else
            {

                $this->flashMessage($returnMessage->msg, 'error');
                $this->redirect('this');
            }
		});
    }

	public function createComponentUpdateSensorDataForm(): Form
	{
		return $this->sensorsFormFactory->createUpdateData(function (Form $form, \stdClass $values) {

			$returnMessage = $this->databaseSelectionManager->createMultiSelection(intval($values->number), new DateTime($values->from), new DateTime($values->to));
			if($returnMessage->state)
			{
				$this->flashMessage($returnMessage->msg, 'success');
				$this->redirect('this',$values->number);
			}
			else
			{

				$this->flashMessage($returnMessage->msg, 'error');
				$this->redirect('this',$values->number);
			}
		});
	}

    public function renderEdit(int $number)
    {

        if(!$this->sensorsManager->sensorIsExist($number))
        {
            $this->flashMessage("Tento senzor neexistuje", 'error');
            $this->redirect('Sensors:default');
        }

	    $desciption = $this->sensorsManager->getSensorsNumber($number)->description;

        $this->template->number = $number;
        $this['editSensorForm']->setDefaults(array('number'=>$number, 'oldNumber'=>$number, 'description'=>$desciption));
        $this['updateSensorDataForm']->setDefaults(array('number'=>$number));

    }


    ///////////////////
    //Smazani senzoru
    ///////////////////
    public function createComponentDeleteSensorForm(): Form
    {
		return $this->sensorsFormFactory->createDelete(function (Form $form, \stdClass $values) {
			$returnMessage = $this->sensorsManager->deleteSensor($values->number);
			if($returnMessage->state)
			{
				$this->flashMessage($returnMessage->msg, 'success');
				$this->redirect('SensorsSettings:default');
			}
			else
			{

				$this->flashMessage($returnMessage->msg, 'error');
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
            $this->flashMessage("Senzor s číslem " . $number . " neexistuje!", 'error');
            $this->redirect('SensorsSettings:default');
        }
        $sensor = $this->sensorsManager->getSensorsNumber(intval($number));
        $this->template->sensor = $sensor;
        $this->template->number = $number;
        $this['deleteSensorForm']->setDefaults($sensor);
        

    }



    public function actionDebug()
    {


        $type = DateSerie::AREA_SPLINE;

        $this->template->rawEvents = $rawEvents = $this->thisSensorManager->getAllEvents('1', "2020-05-05 04:01:00", "2020-05-05 14:00:00");

        $interval = 15;

        ($dataChartF = $this->thisChartManager->sensorChartDataState($rawEvents, 'm', $interval, 'FINISHED'));
        dump($dataChartF);

        ($dataChartS = $this->thisChartManager->sensorChartDataState($rawEvents, 'm', $interval, 'STOP'));

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
        $dayChart->addSerie($serie);

        $this->template->dayChart = $dayChart;
       
    }

}

