<?php
declare(strict_types=1);


namespace App\CoreModule\Presenters;

use App\CoreModule\Model\DatabaseTestManager;
use App\TimeManagers\TimeBox;
use Nette\Forms\Form;
use Nette\Utils\DateTime;
use Tracy\Debugger;
use Tracy\Dumper;
use App\Presenters\BasePresenter;



use App\CoreModule\Model\DatabaseSelectionManager;



/**
 * @brief Presenter for testing
 */
final class DatabaseTestPresenter extends BasePresenter
{


	/**
	 * @var DatabaseSelectionManager
	 */
	private $databaseSelectionManager;
	/**
	 * @var DatabaseTestManager
	 */
	private $databaseTestManager;

	public function __construct(
        DatabaseSelectionManager $databaseSelectionManager,
        DatabaseTestManager $databaseTestManager

    )
	{

		$this->databaseSelectionManager = $databaseSelectionManager;
		$this->databaseTestManager = $databaseTestManager;
	}
	

	public function actionDefault()
	{
//		dump($this->databaseTestManager->saveEvent(30, TimeBox::FINISHED, new DateTime()));

		$startTime = new DateTime();
		$startTime->setTime(rand(5, 6), rand(1, 59), rand(1, 59));

		$stopTime = new DateTime();
//		$stopTime->setTime(rand(18, 23), rand(1, 59), rand(1, 59));
		$stopTime->setTime(17, rand(1, 59), rand(1, 59));
		dump($this->databaseTestManager->saveRandomDay(30,  $startTime, $stopTime));

	}




}

