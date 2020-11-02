<?php

declare(strict_types=1);

namespace App\CoreModule\Component\StatusBubblesControl;

use App\CoreModule\Model\MultiSensorsManager;
use App\CoreModule\Model\ThisSensorManager;
use App\CoreModule\Model\ThisChartManager;
use App\CoreModule\Model\WorkShiftManager;


class StatusBubblesControlFactory
{

	/**
	 * @var MultiSensorsManager
	 */
	private $multiSensorsManager;
	/**
	 * @var ThisSensorManager
	 */
	private $thisSensorManager;


	public function __construct(MultiSensorsManager $multiSensorsManager, ThisSensorManager $thisSensorManager)
    {
	    $this->multiSensorsManager = $multiSensorsManager;
	    $this->thisSensorManager = $thisSensorManager;
    }

    public function create()
    {
        return new StatusBubblesControl($this->multiSensorsManager, $this->thisSensorManager);
    }
}