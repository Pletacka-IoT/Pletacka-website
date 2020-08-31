<?php

declare(strict_types=1);

namespace App\CoreModule\Presenters;
use App\Presenters\BasePresenter;
use App\CoreModule\Model\SensorsManager;
use App\CoreModule\Model\ChartManager;
use App\CoreModule\Model\RoomManager;
use App\CoreModule\Model\WorkShiftManager;

use App\CoreModule\Forms\WorkShiftFormFactory;

use Latte;
use Nette\Application\UI\Form;


/**
 * @brief Homepage presenter
 */
final class WorkShiftPresenter extends BasePresenter
{
    private $sensorsManager;
    private $chartManager;
    private $roomManager;
    private $workShiftManager;
    private $workShiftFormFactory;
//    private $9

	public function __construct(SensorsManager $sensorsManager, ChartManager $chartManager, RoomManager $roomManager, WorkShiftManager $workShiftManager, WorkShiftFormFactory $workShiftFormFactory)
	{
		$this->sensorsManager = $sensorsManager;
		$this->chartManager = $chartManager;
		$this->roomManager = $roomManager;
		$this->workShiftManager = $workShiftManager;
		$this->workShiftFormFactory = $workShiftFormFactory;

    }

    public function createComponentWorkShiftForm(): Form
    {
        return $this->workShiftFormFactory->createWSselect(function (Form $form, \stdClass $values) {
            if($values->ws == "c")
            {
                $wsFirst = "Cahovi";
                $wsSecond = "Vaňkovi";
            }
            else
            {
                $wsFirst = "Vaňkovi";
                $wsSecond = "Cahovi";
            }

            $ret = $this->workShiftManager->setYear($values->year, $wsFirst, $wsSecond);

            $this->flashMessage($ret, "success");

        });
    }

    public function renderDefault() : void
    {
//        dump($this->workShiftManager->getWS(2020, 1));




    }

    public function renderTest() : void
    {

        dump($this->workShiftManager->getActualWS());

    }
}
