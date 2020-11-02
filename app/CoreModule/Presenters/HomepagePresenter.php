<?php

declare(strict_types=1);

namespace App\CoreModule\Presenters;
use App\Presenters\BasePresenter;
use App\CoreModule\Model\SensorsManager;
use App\CoreModule\Model\ChartManager;
use App\CoreModule\Model\RoomManager;
use App\CoreModule\Model\WorkShiftManager;
use App\CoreModule\Component\StatusBubblesControl\StatusBubblesControlFactory;
use Latte;


/**
 * @brief Homepage presenter
 */
final class HomepagePresenter extends BasePresenter
{
    private $sensorsManager;
    private $chartManager;
    private $roomManager;
    private $workShiftManager;
	/**
	 * @var StatusBubblesControlFactory
	 */
	private $statusBubblesControlFactory;


	public function __construct(SensorsManager $sensorsManager, ChartManager $chartManager, RoomManager $roomManager, WorkShiftManager $workShiftManager, StatusBubblesControlFactory $statusBubblesControlFactory)
	{
		$this->sensorsManager = $sensorsManager;
		$this->chartManager = $chartManager;
		$this->roomManager = $roomManager;
		$this->workShiftManager = $workShiftManager;
		$this->statusBubblesControlFactory = $statusBubblesControlFactory;
	}


	protected function createComponentStatusBubbles()
	{
		return $this->statusBubblesControlFactory->create();
	}

	public function handleReloadBubbles(): void
	{
//		$this->redrawControl('sBubbles');
//		$this->redrawControl('sPusInfo');
	}

    public function renderDefault() : void
    {
        $this->template->settings = $this->sensorsManager->getTitleSettings();

        $this->template->actualWS = $this->workShiftManager->getActualWS();

        // Info table
        ($pletackyAll = $this->template->pletackyAll = $this->chartManager->sensorsChartHomepage());

        // Big pletacka room
	    $this->template->plBig = $plBig = $this->roomManager->roomPletarnaBig;
        ($bubblesBig = $this->template->bubblesBig = $this->chartManager->sensorsChartBubbles($plBig));

        // Small pletacka room
	    $this->template->plSmall = $plSmall = $this->roomManager->roomPletarnaSmall;
        ($bubblesSmall = $this->template->bubblesSmall = $this->chartManager->sensorsChartBubbles($plSmall));

//        $last = $pletackyAll["ALL_SENSORS"]["Pletacka1"];
//        dump($last[array_key_last($last)]->state);



    }
}
