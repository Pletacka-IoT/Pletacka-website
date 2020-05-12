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
        $settings = $this->sensorsManager->getTitleSettings();
        if($settings[0])
        {
            $this->template->settings = $settings[1];
        }
        else
        {
            $this->error($settings[2]);
        }
        
        $variab = "Pepik";
        $this->template->var = $variab;
    }
}
