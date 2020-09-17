<?php

declare(strict_types=1);

namespace App\CoreModule\Controls\PletackaChartControl;

//use App\CoreModule\Controls\PletackaChartControl;
use App\CoreModule\Model\ThisSensorManager;
use App\CoreModule\Model\ThisChartManager;

class PletackaChartControlFactory
{

    private $thisChartManager;
    private $thisSensorManager;


    public function __construct(ThisSensorManager $thisSensorManager, ThisChartManager $thisChartManager)
    {
        $this->thisSensorManager = $thisSensorManager;
        $this->thisChartManager = $thisChartManager;
    }

    public function create($sNumber)
    {
        return new PletackaChartControl($sNumber, $this->thisSensorManager, $this->thisChartManager/*$inNumber*/);
    }
}