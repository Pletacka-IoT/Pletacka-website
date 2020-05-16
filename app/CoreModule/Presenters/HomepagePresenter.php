<?php

declare(strict_types=1);

namespace App\CoreModule\Presenters;
use App\Presenters\BasePresenter;
use App\CoreModule\Model\SensorsManager;



final class HomepagePresenter extends BasePresenter
{
    private $sensorsManager;

	public function __construct(SensorsManager $sensorsManager)
	{
		$this->sensorsManager = $sensorsManager;
    }

    public function renderDefault() : void
    {
        $this->template->settings = $this->sensorsManager->getTitleSettings();
        
        $variab = "Pepik";
        $this->template->var = $variab;
    }
}
