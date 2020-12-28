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


	public function test()
	{
		$date = '2020-12-01';
		$number = 30;

		$this->sensorsManager->deleteSensor($number);
		$this->sensorsManager->addNewSensor($number);



		$startTime = new DateTime($date);
//		$startTime->setTime(rand(5, 6), rand(1, 59), rand(1, 59));
//		$startTime->setTime(rand(5, 6), rand(1, 59));
		$startTime->setTime(1, 0);

		$endTime = new DateTime($date);
		$endTime->setTime(22, 0);
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
//		dump($bX);

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


	public function debug()
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
//		dump($bX);

		$b = 0;
		foreach ($bX as $x) {
			$b += $x->stopTime;
		}
		echo("STOP-Calc = ".$b."<br>");

	}


	public function generate(DateTime $startTime, int $days)
	{
		$number = 30;


//		$startTime = new DateTime("2020-12-01");
		for($i = 0; $i<$days; $i++)
		{

			$startTime->setTime(rand(5, 6), rand(1, 59), rand(1, 59));
//			$startTime->setTime(rand(5, 6), rand(1, 59));
//			$startTime->setTime(9, 0);

			$endTime = clone $startTime;
//			$endTime->setTime(11, 0);
			$endTime->setTime(rand(20, 22), rand(1, 59), rand(1, 59));
			$gen = ($this->databaseTestManager->generateRandomDay($number,  $startTime, $endTime));

			$startTime->add(\DateInterval::createFromDateString("1 day"));
		}

	}

	public function testDaysGen()
	{
		$number = 14;
		$days =30;

//		$this->sensorsManager->deleteSensor($number);
//		$this->sensorsManager->addNewSensor($number);

		$from = new DateTime("2020-11-09");
		$to = clone $from;
		$to->add(\DateInterval::createFromDateString($days." days"));

//		$this->databaseTestManager->generateRandomDaysFromToCountDays($number,clone $from, $days);

//		dump($this->databaseSelectionManager->createSelectionFromTo($number, DatabaseSelectionManager::HOUR_L, $from, $to));
		dump($this->databaseSelectionManager->createSelectionFromTo($number, DatabaseSelectionManager::DAY_L, $from, $to));
		dump($this->databaseSelectionManager->createSelectionFromTo($number, DatabaseSelectionManager::MONTH_L, $from, $to));
		dump($this->databaseSelectionManager->createSelectionFromTo($number, DatabaseSelectionManager::YEAR_L, $from, $to));

		dump($to);

	}

	public function testDays()
	{
		$number = 14;
		$days =50;

		$from = new DateTime("2020-11-09");
		$to = clone $from;
		$to->add(\DateInterval::createFromDateString($days." days"));


		dump($this->databaseSelectionManager->createSelectionFromTo($number, DatabaseSelectionManager::HOUR_L, $from, $to));
		dump($this->databaseSelectionManager->createSelectionFromTo($number, DatabaseSelectionManager::DAY_L, $from, $to));
		dump($this->databaseSelectionManager->createSelectionFromTo($number, DatabaseSelectionManager::MONTH_L, $from, $to));
		dump($this->databaseSelectionManager->createSelectionFromTo($number, DatabaseSelectionManager::YEAR_L, $from, $to));

		dump($to);

	}

	public function testDaysAuto()
	{
		$number = 14;


		$from = new DateTime("2020-11-09");
		$to = new DateTime("2020-12-29");

		dump($this->databaseSelectionManager->createMultiSelection($number, $from, $to));

		dump($to);

	}


	public function testMultiRen()
	{
		$sensors = $this->sensorsManager->getSensors();

		$from = new DateTime("2020-11-07");
		$to = new DateTime("2020-11-09");
		$num = 17;

//		$ret = $this->databaseSelectionManager->createMultiSelection($num, $from, $to);



		$ret = $this->databaseSelectionManager->createMultiSelectionForSensorsFromTo($sensors, $from, $to);

		dump($ret);
	}

	public function actionDefault()
	{


//		$this->test();

//		for($i = 0; $i<40; $i++)
//		{
//			$this->test();
//		}

//		$this->debug();


//		$this->testDaysAuto();
//		$this->testDays();


//		(new DateTime("2020-12-01"), 4);

		$this->testMultiRen();


	}




}

