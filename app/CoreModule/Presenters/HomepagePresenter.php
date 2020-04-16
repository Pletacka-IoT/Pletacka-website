<?php

declare(strict_types=1);

namespace App\CoreModule\Presenters;
use App\Presenters\BasePresenter;
use App\CoreModule\Model\SensorManager;



final class HomepagePresenter extends BasePresenter
{
    private $sensorManager;

	public function __construct(SensorManager $sensorManager)
	{
		$this->sensorManager = $sensorManager;
    }

    public function renderDefault() : void
    {
        $this->template->settings = $this->sensorManager->getTitleSettings();
        $variab = "Pepik";
        $this->template->var = $variab;
    }
}
