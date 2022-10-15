<?php
declare(strict_types=1);


namespace App\CoreModule\Presenters;

use Nette\Forms\Form;
use Tracy\Debugger;
use Tracy\Dumper;
use App\Presenters\BasePresenter;


use App\CoreModule\Model\SensorsManager;
use App\CoreModule\Model\ThisSensorManager;
use App\CoreModule\Model\WorkShiftManager;
use App\CoreModule\Model\DatabaseSelectionManager;
use App\CoreModule\Model\SetupManager;
use App\CoreModule\Forms\SensorsFormFactory;
use Nette\Http\Request;
use Nette\Utils\DateTime;
use DateInterval;
use Nette\Http\IResponse;

use App\CoreModule\Component\SensorOverview\SensorOverviewFactory;
use App\CoreModule\Component\ThisStatusNumbersControl\ThisStatusNumbersControlFactory;


use App\TimeManagers\TimeBox;



/**
 * @brief Presenter for testing
 */
final class OverviewPresenter extends BasePresenter
{

	
	private $sensorsManager;
    private $request;
    private $urlParameter;
    private $sensorsFormFactory;
    private $thisSensorManager;
    private $workShiftManager;
    private $databaseSelectionManager;
	private $databaseDataExtractorManager;
	/**
	 * @var SetupManager
	 */
	private $setupManager;
	/**
	 * @var SensorOverviewFactory
	 */
	private SensorOverviewFactory $sensorOverviewFactory;
	/**
	 * @var ThisStatusNumbersControlFactory
	 */
	private ThisStatusNumbersControlFactory $thisStatusNumbersControlFactory;

	public function __construct(
	    SensorsManager $sensorsManager,
        ThisSensorManager $thisSensorManager,
        Request $request,
        SensorsFormFactory $sensorsFormFactory,
        WorkShiftManager $workShiftManager,
        DatabaseSelectionManager $databaseSelectionManager,
		SetupManager $setupManager,
	    SensorOverviewFactory $sensorOverviewFactory,
	    ThisStatusNumbersControlFactory $thisStatusNumbersControlFactory
    )
	{
        
        $this->sensorsManager = $sensorsManager;
        $this->thisSensorManager = $thisSensorManager;
        $this->request = $request;
        $this->sensorsFormFactory = $sensorsFormFactory;
        $this->workShiftManager = $workShiftManager;
        $this->databaseSelectionManager = $databaseSelectionManager;
		$this->setupManager = $setupManager;
		$this->sensorOverviewFactory = $sensorOverviewFactory;
		$this->thisStatusNumbersControlFactory = $thisStatusNumbersControlFactory;
	}

	protected function createComponentSensorOverview()
	{
		return $this->sensorOverviewFactory->create();
	}

	protected function createComponentThisStatusNumbers()
	{
		return $this->thisStatusNumbersControlFactory->create();
	}


	public function renderDefault(): void
    {
	    $this->template->workShift = $this->workShiftManager->getWeekWS();
	    $this->template->sensors = $this->sensorsManager->getSensors();
    }


}

