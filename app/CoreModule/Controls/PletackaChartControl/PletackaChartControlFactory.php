<?php

declare(strict_types=1);

namespace App\CoreModule\Controls\PletackaChartControl;

//use App\CoreModule\Controls\PletackaChartControl;
use App\CoreModule\Model\ThisSensorManager;
use App\CoreModule\Model\ThisChartManager;
use App\CoreModule\Model\WorkShiftManager;


class PletackaChartControlFactory
{

    private $thisChartManager;
    private $thisSensorManager;
    private $workShiftManager;


    public function __construct(ThisSensorManager $thisSensorManager, ThisChartManager $thisChartManager, WorkShiftManager $workShiftManager)
    {
        $this->thisSensorManager = $thisSensorManager;
        $this->thisChartManager = $thisChartManager;
        $this->workShiftManager = $workShiftManager;
    }

    public function create($sNumber)
    {
        return new PletackaChartControl($sNumber, $this->thisSensorManager, $this->thisChartManager, $this->workShiftManager/*$inNumber*/);
    }
}