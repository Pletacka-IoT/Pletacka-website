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


use Jakubandrysek\Chart\Category;
use Jakubandrysek\Chart\CategoryChart;
use Jakubandrysek\Chart\Serie\CategorySerie;
use Jakubandrysek\Chart\Segment\CategorySegment;
use Jakubandrysek\Chart\DonutChart;
use Jakubandrysek\Chart\Segment\DonutSegment;
use Jakubandrysek\Chart\PieChart;
use Jakubandrysek\Chart\Segment\PieSegment;
use Jakubandrysek\Chart\Chart;
use Jakubandrysek\Chart\Serie\Serie;
use Jakubandrysek\Chart\Segment\Segment;
use Jakubandrysek\Chart\DateChart;
use Jakubandrysek\Chart\Serie\DateSerie;
use Jakubandrysek\Chart\Segment\DateSegment;
use DateTimeImmutable;

use Jakubandrysek\Chart\BasicChart;

use App\TimeManagers\TimeBox;



/**
 * @brief Presenter for testing
 */
final class SetupPresenter extends BasePresenter
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

	public function __construct(
	    SensorsManager $sensorsManager,
        ThisSensorManager $thisSensorManager,
        Request $request,
        SensorsFormFactory $sensorsFormFactory,
        WorkShiftManager $workShiftManager,
        DatabaseSelectionManager $databaseSelectionManager,
		SetupManager $setupManager
    )
	{
        
        $this->sensorsManager = $sensorsManager;
        $this->thisSensorManager = $thisSensorManager;
        $this->request = $request;
        $this->sensorsFormFactory = $sensorsFormFactory;
        $this->workShiftManager = $workShiftManager;
        $this->databaseSelectionManager = $databaseSelectionManager;
		$this->setupManager = $setupManager;
	}
	



	public function renderDefault(): void
    {
		$this->template->setup = $ret = $this->setupManager->importantTablesAreExist();
//		dump($ret);
    }


}

