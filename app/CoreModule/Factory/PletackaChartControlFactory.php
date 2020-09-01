<?php

declare(strict_types=1);

namespace App\CoreModule\Factory;

use App\CoreModule\Control\PletackaChartControl;

class PletackaChartControlFactory
{

    public function __construct()
    {

    }

    public function create(int $poolId)
    {
        return new PletackaChartControl($poolId);
    }
}