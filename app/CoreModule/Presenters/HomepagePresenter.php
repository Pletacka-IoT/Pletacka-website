<?php

declare(strict_types=1);

namespace App\CoreModule\Presenters;
use App\Presenters\BasePresenter;
use App\CoreModule\Model\SensorsManager;
use App\CoreModule\Model\ChartManager;
use App\CoreModule\Model\RoomManager;
use Latte;


/**
 * @brief Homepage presenter
 */
final class HomepagePresenter extends BasePresenter
{
    private $sensorsManager;
    private $chartManager;
    private $roomManager;
//    private $9

	public function __construct(SensorsManager $sensorsManager, ChartManager $chartManager, RoomManager $roomManager)
	{
		$this->sensorsManager = $sensorsManager;
		$this->chartManager = $chartManager;
		$this->roomManager = $roomManager;

    }

    public function renderDefault() : void
    {
        $this->template->settings = $this->sensorsManager->getTitleSettings();

        ($pletackyAll = $this->template->pletackyAll = $this->chartManager->sensorsChartHomepage());

        $plBig = $this->roomManager->roomPletarnaBig;
        ($bubblesBig = $this->template->bubblesBig = $this->chartManager->sensorsChartBubbles($plBig));

        $plSmall = $this->roomManager->roomPletarnaSmall;
        ($bubblesSmall = $this->template->bubblesSmall = $this->chartManager->sensorsChartBubbles($plSmall));

//        $last = $pletackyAll["ALL_SENSORS"]["Pletacka1"];
//        dump($last[array_key_last($last)]->state);



    }
}
