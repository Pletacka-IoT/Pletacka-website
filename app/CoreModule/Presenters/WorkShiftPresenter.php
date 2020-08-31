<?php

declare(strict_types=1);

namespace App\CoreModule\Presenters;
use App\Presenters\BasePresenter;
use App\CoreModule\Model\SensorsManager;
use App\CoreModule\Model\ChartManager;
use App\CoreModule\Model\RoomManager;
use App\CoreModule\Model\WorkShiftManager;
use Latte;


/**
 * @brief Homepage presenter
 */
final class WorkShiftPresenter extends BasePresenter
{
    private $sensorsManager;
    private $chartManager;
    private $roomManager;
    private $workShiftManager;
//    private $9

	public function __construct(SensorsManager $sensorsManager, ChartManager $chartManager, RoomManager $roomManager, WorkShiftManager $workShiftManager)
	{
		$this->sensorsManager = $sensorsManager;
		$this->chartManager = $chartManager;
		$this->roomManager = $roomManager;
		$this->workShiftManager = $workShiftManager;

    }

    public function renderDefault() : void
    {
        dump($this->workShiftManager->getWS(2020, 1));
        dump($this->workShiftManager->setWS(2020, 1, ));


    }

    public function renderTest() : void
    {


    }
}
