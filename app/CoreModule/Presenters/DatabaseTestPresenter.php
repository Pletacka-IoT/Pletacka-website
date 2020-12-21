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
use App\CoreModule\Model\SensorsManager;



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
	/**
	 * @var SensorsManager
	 */
	private $sensorsManager;

	public function __construct(
        DatabaseSelectionManager $databaseSelectionManager,
        DatabaseTestManager $databaseTestManager,
		SensorsManager $sensorsManager

    )
	{

		$this->databaseSelectionManager = $databaseSelectionManager;
		$this->databaseTestManager = $databaseTestManager;
		$this->sensorsManager = $sensorsManager;
	}


	public function a()
	{
		$date = '2020-12-01';
		$number = 30;

		$this->sensorsManager->deleteSensor($number);
		$this->sensorsManager->addNewSensor($number);



		$startTime = new DateTime($date);
//		$startTime->setTime(rand(5, 6), rand(1, 59), rand(1, 59));
//		$startTime->setTime(rand(5, 6), rand(1, 59));
		$startTime->setTime(9, 0);

		$endTime = new DateTime($date);
		$endTime->setTime(11, 0);
		$gen = ($this->databaseTestManager->generateRandomDay($number,  $startTime, $endTime));

		$datum = new DateTime($date);
		for($i = 0; $i<24; $i++)
		{
			$x = $i;
//	            if($x<10){$x = "0".$x;}
			$datum->setTime($i, 5);
			$returnMessage =  $this->databaseSelectionManager->createSelection($number, DatabaseSelectionManager::HOUR, $datum);
		}

		$returnMessage =  $this->databaseSelectionManager->createSelection($number, DatabaseSelectionManager::DAY, $datum);


		$a = $gen->main["STOP_TIME"];
		$startTime->setTime(0,0);
		$bX = $this->databaseSelectionManager->getSelectionDataDetail($number, DatabaseSelectionManager::DAY, null, $startTime, $endTime);
		dump($bX);

		$b = 0;
		foreach ($bX as $x) {
			$b += $x->stopTime;
		}
//		$b = $bX[0]->stopTime + $bX[1]->stopTime;

		if($a == $b)
		{
			echo("OK ->    STOP-Gen = ".$a." == STOP-Calc = ".$b."<br>");
		}
		else
		{
			echo("ERROR ->      STOP-Gen = ".$a." == STOP-Calc = ".$b."<br>");
		}

	}


	public function b()
	{
		$date = '2020-12-01';
		$number = 30;

//		$this->sensorsManager->deleteSensor($number);
//		$this->sensorsManager->addNewSensor($number);



		$startTime = new DateTime($date);
//		$startTime->setTime(rand(5, 6), rand(1, 59), rand(1, 59));
//		$startTime->setTime(rand(5, 6), rand(1, 59));
		$startTime->setTime(9, 0);

		$endTime = new DateTime($date);
		$endTime->setTime(11, 0);
//		$gen = ($this->databaseTestManager->generateRandomDay($number,  $startTime, $endTime));

		$datum = new DateTime($date);
		for($i = 0; $i<24; $i++)
		{
			$x = $i;
//	            if($x<10){$x = "0".$x;}
			$datum->setTime($i, 5);
			$returnMessage =  $this->databaseSelectionManager->createSelection($number, DatabaseSelectionManager::HOUR, $datum);
		}

		$returnMessage =  $this->databaseSelectionManager->createSelection($number, DatabaseSelectionManager::DAY, $datum);



		$startTime->setTime(0,0);
		$bX = $this->databaseSelectionManager->getSelectionDataDetail($number, DatabaseSelectionManager::DAY, null, $startTime, $endTime);
		dump($bX);

		$b = 0;
		foreach ($bX as $x) {
			$b += $x->stopTime;
		}
		echo("STOP-Calc = ".$b."<br>");

	}

	public function actionDefault()
	{


//		$this->a();

		$this->b();


//		$number = 30;
//		$date = '2020-12-01';
//		$dateTo = '2020-12-02';
//
//		$startTime = new DateTime($date);
//		$startTime->setTime(9, 0);
//
//		$endTime = new DateTime($dateTo);
//		$endTime->setTime(11, 0);
//
//		$this->databaseTestManager->generateRandomByDates($number, $startTime, $endTime);






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
//			/*dump*/($this->databaseTestManager->generateRandomDay($number,  $startTime, $stopTime));
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



//*****************************************************
////AAAAAAAAAAAAAAAAAAA




//
//		$date = '2020-12-01';
//		$number = 30;
//
//		$this->sensorsManager->deleteSensor($number);
//		$this->sensorsManager->addNewSensor($number);
//
//
//
//		$startTime = new DateTime($date);
////		$startTime->setTime(rand(5, 6), rand(1, 59), rand(1, 59));
////		$startTime->setTime(rand(5, 6), rand(1, 59));
//		$startTime->setTime(9, 0);
//
//		$endTime = new DateTime($date);
//		$endTime->setTime(11, 0);
////		$gen = ($this->databaseTestManager->generateRandomDay($number,  $startTime, $endTime));
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
//
//
//		$a = 1860;//$gen->main["STOP_TIME"];
//		$startTime->setTime(0,0);
//		$bX = $this->databaseSelectionManager->getSelectionDataDetail($number, DatabaseSelectionManager::DAY, null, $startTime, $endTime);
//		dump($bX);
//
//		$b = 0;
//		foreach ($bX as $x) {
//			$b += $x->stopTime;
//		}
////		$b = $bX[0]->stopTime + $bX[1]->stopTime;
//
//		if($a == $b)
//		{
//			echo("OK ->    STOP-Gen = ".$a." == STOP-Calc = ".$b."<br>");
//		}
//		else
//		{
//			echo("ERROR ->      STOP-Gen = ".$a." == STOP-Calc = ".$b."<br>");
//		}
//AAAAAAAAAAAAAAAAAAAAAA
//*****************************************************





//*****************************************************






//		for($i = 0 ; $i< 10; $i ++)
//		{
//			$date = '2020-12-01';
//			$number = 30;
////			dump($this->sensorsManager->deleteSensor($number));
//			($this->sensorsManager->deleteSensor($number));
//
//
//			$this->sensorsManager->addNewSensor($number);
//
//			$startTime = new DateTime($date);
//			$startTime->setTime(8, 0 );
//
//			$endTime = new DateTime($date);
//			$endTime->setTime(9, 0 );
////			dump($gen = $this->databaseTestManager->generateRandomDay($number,  $startTime, $endTime));
//			($gen = $this->databaseTestManager->generateRandomDay($number,  $startTime, $endTime));
//
//
//			$datum = new DateTime($date);
//			$datum->setTime(8, 5);
//			$returnMessage =  $this->databaseSelectionManager->createSelection($number, DatabaseSelectionManager::HOUR, $datum);
//			$returnMessage =  $this->databaseSelectionManager->createSelection($number, DatabaseSelectionManager::DAY, $datum);
//
//			$a = $gen->main["STOP_TIME"];
//
//			$b = $this->databaseSelectionManager->getSelectionDataDetail($number, DatabaseSelectionManager::HOUR, null, $startTime, $endTime)[0]->stopTime;
//			if($a == $b)
//			{
//				echo("OK ->    STOP-Gen = ".$a." == STOP-Calc = ".$b."<br>");
//			}
//			else
//			{
//				echo("ERROR ->      STOP-Gen = ".$a." == STOP-Calc = ".$b."<br>");
//			}
//		}




//*****************************************************


//		$date = '2020-12-01';
//		$number = 31;

//		$number = 30;
//		$date = '2020-12-01';
//		$dateTo = '2020-12-02';
//
//		$startTime = new DateTime($date);
//		$startTime->setTime(9, 0);
//
//		$endTime = new DateTime($dateTo);
//		$endTime->setTime(11, 0);
//
//		$datum = new DateTime($date);
//		$datum->setTime(8, 5);
//		$returnMessage =  $this->databaseSelectionManager->createSelection($number, DatabaseSelectionManager::HOUR, $datum);
//		dump($returnMessage);
//
//		$auto = $this->databaseSelectionManager->getSelectionDataDetail($number, DatabaseSelectionManager::HOUR, null, $startTime, $endTime);
//		echo("STOP-GEN = ".$auto[0]->stopTime."<br>");
//

	}




}

