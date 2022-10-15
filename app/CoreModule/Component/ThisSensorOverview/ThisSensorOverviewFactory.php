<?php

declare(strict_types=1);

namespace App\CoreModule\Component\ThisSensorOverview;

use App\CoreModule\Model\DatabaseSelectionManager;
use App\CoreModule\Model\MultiSensorsManager;
use App\CoreModule\Model\ThisSensorManager;
use App\CoreModule\Model\ThisChartManager;
use App\CoreModule\Model\WorkShiftManager;
use Nette\Database\Context;


class ThisSensorOverviewFactory
{

	/**
	 * @var MultiSensorsManager
	 */
	private $multiSensorsManager;
	/**
	 * @var ThisSensorManager
	 */
	private $thisSensorManager;
	/**
	 * @var Context
	 */
	private $database;
	/**
	 * @var DatabaseSelectionManager
	 */
	private $databaseSelectionManager;
	/**
	 * @var WorkShiftManager
	 */
	private $workShiftManager;


	public function __construct(MultiSensorsManager $multiSensorsManager,
	                            ThisSensorManager $thisSensorManager,
	                            Context $database,
	                            DatabaseSelectionManager $databaseSelectionManager,
								WorkShiftManager $workShiftManager)
    {
	    $this->multiSensorsManager = $multiSensorsManager;
	    $this->thisSensorManager = $thisSensorManager;
	    $this->database = $database;
	    $this->databaseSelectionManager = $databaseSelectionManager;
	    $this->workShiftManager = $workShiftManager;
    }

    public function create()
    {
        return new ThisSensorOverview($this->multiSensorsManager, $this->thisSensorManager, $this->database, $this->databaseSelectionManager, $this->workShiftManager);
    }
}