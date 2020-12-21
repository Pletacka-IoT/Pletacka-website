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
////		dump($this->databaseTestManager->saveEvent(30, TimeBox::FINISHED, new DateTime()));
//
//		// Start date
//		$date = '2020-12-01';
//		// End date
////		$end_date = '2020-12-20';
//		$end_date = '2020-12-20';
//
//		while (strtotime($date) <= strtotime($end_date)) {
//
//			$number = 33;
//
//			$startTime = new DateTime($date);
//			$startTime->setTime(rand(5, 6), rand(1, 59), rand(1, 59));
//
//			$stopTime = new DateTime($date);
//			$stopTime->setTime(rand(20, 22), rand(1, 59), rand(1, 59));
//			/*dump*/($this->databaseTestManager->saveRandomDay($number,  $startTime, $stopTime));
//
//			$datum = new DateTime($date);
//			for($i = 0; $i<24; $i++)
//	        {
//	            $x = $i;
////	            if($x<10){$x = "0".$x;}
//	            $datum->setTime($i, 5);
//	            $returnMessage =  $this->databaseSelectionManager->createSelection($number, DatabaseSelectionManager::HOUR, $datum);
//	        }
//
//			$returnMessage =  $this->databaseSelectionManager->createSelection($number, DatabaseSelectionManager::DAY, $datum);
//
//			echo "$date<br>";
//			$date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
//		}

		//		dump($this->databaseTestManager->saveEvent(30, TimeBox::FINISHED, new DateTime()));




//		$date = '2020-12-01';
//		$number = 31;
//
//		$startTime = new DateTime($date);
//		$startTime->setTime(rand(5, 6), rand(1, 59), rand(1, 59));
//
//		$stopTime = new DateTime($date);
//		$stopTime->setTime(rand(20, 22), rand(1, 59), rand(1, 59));
//		/*dump*/($this->databaseTestManager->saveRandomDay($number,  $startTime, $stopTime));
//
//		$datum = new DateTime($date);
//		for($i = 0; $i<24; $i++)
//		{
//			$x = $i;
////	            if($x<10){$x = "0".$x;}
//			$datum->setTime($i, 5);
//			$returnMessage =  $this->databaseSelectionManager->createSelection($number, DatabaseSelectionManager::HOUR, $datum);
//		}
//
//		$returnMessage =  $this->databaseSelectionManager->createSelection($number, DatabaseSelectionManager::DAY, $datum);




		$date = '2020-12-01';
		$number = 30;

		$startTime = new DateTime($date);
		$startTime->setTime(8, 0 );

		$endTime = new DateTime($date);
		$endTime->setTime(9, 0 );
		dump($gen = $this->databaseTestManager->saveRandomDay($number,  $startTime, $endTime));


		$datum = new DateTime($date);
		$datum->setTime(8, 5);
		$returnMessage =  $this->databaseSelectionManager->createSelection($number, DatabaseSelectionManager::HOUR, $datum);
		$returnMessage =  $this->databaseSelectionManager->createSelection($number, DatabaseSelectionManager::DAY, $datum);

		echo("STOP-GEN = ".$gen->main["STOP_TIME"]."<br>");

		$auto = $this->databaseSelectionManager->getSelectionDataDetail($number, DatabaseSelectionManager::HOUR, null, $startTime, $endTime);
		echo("STOP-GEN = ".$auto[0]->stopTime."<br>");



	}




}

