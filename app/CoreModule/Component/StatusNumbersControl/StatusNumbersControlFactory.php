<?php

declare(strict_types=1);

namespace App\CoreModule\Component\StatusNumbersControl;

use App\CoreModule\Model\MultiSensorsManager;
use App\CoreModule\Model\ThisSensorManager;
use App\CoreModule\Model\ThisChartManager;
use App\CoreModule\Model\WorkShiftManager;
use Nette\Database\Context;


class StatusNumbersControlFactory
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


	public function __construct(MultiSensorsManager $multiSensorsManager,
	                            ThisSensorManager $thisSensorManager,
	                            Context $database)
    {
	    $this->multiSensorsManager = $multiSensorsManager;
	    $this->thisSensorManager = $thisSensorManager;
	    $this->database = $database;
    }

    public function create()
    {
        return new StatusNumbersControl($this->multiSensorsManager, $this->thisSensorManager, $this->database);
    }
}