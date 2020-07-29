<?php

declare(strict_types=1);

namespace App\CoreModule\Presenters;
use App\Presenters\BasePresenter;
use App\CoreModule\Model\SensorsManager;
use App\CoreModule\Model\ChartManager;



final class HomepagePresenter extends BasePresenter
{
    private $sensorsManager;
    private $chartManager;

	public function __construct(SensorsManager $sensorsManager, ChartManager $chartManager)
	{
		$this->sensorsManager = $sensorsManager;
		$this->chartManager = $chartManager;
    }

    public function renderDefault() : void
    {
        $this->template->settings = $this->sensorsManager->getTitleSettings();

        dump($this->template->pletackyAll = $this->chartManager->sensorsChartHomepage());
    }
}
