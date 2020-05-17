<?php
declare(strict_types=1);


namespace App\CoreModule\Presenters;

use Nette\Forms\Form;
use Tracy\Debugger;
use Tracy\Dumper;
use App\Presenters\BasePresenter;


use App\CoreModule\Model\SensorsManager;
use App\CoreModule\Model\ThisSensorManager;
use App\CoreModule\Forms\SensorsFormFactory;
use Nette\Http\Request;
use Nette\Utils\DateTime;
use DateInterval;
use Nette\Http\IResponse;


use Jakubandrysek\Chart\Category;
use Jakubandrysek\Chart\CategoryChart;
use Jakubandrysek\Chart\Serie\CategorySerie;
use Jakubandrysek\Chart\Segment\CategorySegment;
use Jakubandrysek\Chart\DonutChart;
use Jakubandrysek\Chart\Segment\DonutSegment;
use Jakubandrysek\Chart\PieChart;
use Jakubandrysek\Chart\Segment\PieSegment;
use Jakubandrysek\Chart\Chart;
use Jakubandrysek\Chart\Serie\Serie;
use Jakubandrysek\Chart\Segment\Segment;
use Jakubandrysek\Chart\DateChart;
use Jakubandrysek\Chart\Serie\DateSerie;
use Jakubandrysek\Chart\Segment\DateSegment;
use DateTimeImmutable;

use Jakubandrysek\Chart\BasicChart;

use App\TimeManagers\TimeBox;




final class TestPresenter extends BasePresenter
{
	// const
    //     OLDWORK = '1',	// Machine is working
    //     OLDSTOP = "0",	// Machine is not working
    //     REWORK = "2"; 	//State after end of stoji	
	
	private $sensorsManager;
    private $request;
    private $urlParameter;
    private $sensorsFormFactory;
    private $thisSensorManager;

	public function __construct(SensorsManager $sensorsManager, ThisSensorManager $thisSensorManager, Request $request,  SensorsFormFactory $sensorsFormFactory)
	{
        
        $this->sensorsManager = $sensorsManager;
        $this->thisSensorManager = $thisSensorManager;
        $this->request = $request;
        $this->sensorsFormFactory = $sensorsFormFactory;
	}
	
	public function runSafely($array)
	{
		if(is_array($array)&&count($array)>=4)
		{
			if($array[0])
			{
				return $array[1];
			}
			else
			{
				$this->error($array[2], IResponse::S405_METHOD_NOT_ALLOWED);
			}
		}
	}
	
	public function renderDefault()
	{
		$this->thisSensorManager->testPretty();
		// //$this->thisSensorManager->resetDB($sName);

		// $ids = $this->thisSensorManager->getAllIdState($sName, '2020-04-24 22:00:00', '2020-04-24 22:30:00', $this->thisSensorManager::OLDWORK);
		// $runTime = $this->thisSensorManager->getWorkTime($sName,$ids);
		

        // $this->template->allEventsCount = $this->thisSensorManager->countAllEvents($sName);
        // $this->template->allEventsCount0 = $this->thisSensorManager->countAllEventsState($sName, $this->thisSensorManager::OLDSTOP);
		// $this->template->allEventsCount1 = $this->thisSensorManager->countAllEventsState($sName, $this->thisSensorManager::OLDWORK);
		// $this->template->allEvents = $this->thisSensorManager->getAllEvents($sName);
		
		// $this->template->firstEvent = $this->thisSensorManager->getFirstIdState($sName, '2020-04-24 22:00:00', $this->thisSensorManager::OLDWORK);
		// $this->template->lastEvent = $this->thisSensorManager->getLastIdState($sName, '2020-04-24 22:20:00', $this->thisSensorManager::OLDWORK);
		// $this->thisSensorManager::MINUTE;
		

		// //dump(date_diff(	DateTime::from("2020-04-24 22:13:00"), DateTime::from("2020-04-24 22:03:00")));


		// $this->template->runTime = $runTime;
		// $this->template->runTimeAdd = DateTime::from("2000-01-01 00:00:00")->add($runTime);

		// $allTime = $this->thisSensorManager->getAllTime($sName,$ids);
		// $this->template->allTime = $allTime;
		// $this->template->allTimeAdd = DateTime::from("2000-01-01 00:00:00")->add($allTime);

		// // dump($this->thisSensorManager->timeToInterval("00:03:18"));

	

		


		
        
        // echo "XXX:".$this->thisSensorManager->getAllEventsOlder($sName, '2020-04-17 15:56:30')."<br>";
        // echo "Time<br>";
        // foreach($this->thisSensorManager->getAllEventsYounger($sName, '2020-04-17 15:52:30') as $event)
        // {
        //     echo $event->id."->". $event->state."->".$event->time."<br>";
        // }

        // echo "All<br>";
        // foreach($this->thisSensorManager->getAllEvents($sName) as $event)
        // {
        //     echo $event->id."->". $event->state."->".$event->time."<br>";
        // }
	}

	public function actionDebug($name)
	{

        $sName = "Debug";

		$start = "2020-05-05 4:00:00";
		$end = "2020-05-05 7:00:00";

        $this->template->rawEvents = $rawEvents = $this->thisSensorManager->getAllEvents($sName, '2020-01-01 6:54:00', '2020-05-05 9:00:00');
		
//		dump($events);
		if($rawEvents)
        {
            $events = new TimeBox($rawEvents);

            $this->template->events = $events->getEvents();

            $this->template->countAll = $events->countEvents();
            $this->template->countFinished = $events->countEvents(TimeBox::FINISHED);
            $this->template->countStop = $events->countEvents(TimeBox::STOP);
            $this->template->countRework = $events->countEvents(TimeBox::REWORK);
            $this->template->countOn = $events->countEvents(TimeBox::ON);
            $this->template->countOff = $events->countEvents(TimeBox::OFF);
            $this->template->allTime = $events->allTime();
            $this->template->stopTime = $events->stopTime();
            $this->template->workTime = $events->workTime();
            $this->template->avgStopTime = $events->avgStopTime();
            $this->template->avgWorkTime = $events->avgWorkTime();
        }


//		$start = Datetime::from("2000-01-01 00:00:00");

	}
	
	public function renderRun(): void
	{



		////////////////////////////////////////////////////

		// $chart = new Chart();

		// $serie = new Serie(Serie::LINE, 'Serie 1', 'red');
		// $serie->addSegment(new Segment(5, 10));
		// $serie->addSegment(new Segment(6, 4));
		// $serie->addSegment(new Segment(2, 8));
		// $chart->addSerie($serie);
		
		// $serie = new Serie(Serie::LINE, 'Serie 2');
		// $serie->addSegment(new Segment(2, 8));
		// $serie->addSegment(new Segment(4, 6));
		// $serie->addSegment(new Segment(8, 5));
		// $serie->addSegment(new Segment(7, 7));
		// $chart->addSerie($serie);

		$chart = new CategoryChart([
			new Category("1", 'January'),
			new Category("2", 'February'),
			new Category("3", 'March'),
		]);
		$chart->setValueSuffix(' $');
		
		$serie = new CategorySerie(CategorySerie::BAR, 'Company 1', 'red');
		$serie->addSegment(new CategorySegment("1", 0));
		$serie->addSegment(new CategorySegment("2", 4000));
		$serie->addSegment(new CategorySegment("3", 1000));
		$chart->addSerie($serie, 'group1');
		
		$serie = new CategorySerie(CategorySerie::BAR, 'Company 2', 'green');
		$serie->addSegment(new CategorySegment("1", 3000));
		// Segments could be omitted (default value is 0)
		$serie->addSegment(new CategorySegment("3", 500));
		$chart->addSerie($serie, 'group1');
		
		$serie = new CategorySerie(CategorySerie::LINE, 'Summary');
		$serie->addSegment(new CategorySegment("1", 3000));
		$serie->addSegment(new CategorySegment("3", 1500));
		$serie->addSegment(new CategorySegment("2", 4000));
		$chart->addSerie($serie);
		
		// echo $chart;		

		$this->template->chart = $chart;

		//////////////////////////////////////////////

		$chart = new DateChart();
		$chart->setValueSuffix(' $');
		//$chart->enableTimePrecision(); // Enable time accurate to seconds
		
		$serie = new DateSerie(DateSerie::LINE, 'Revenues', 'green');
		$serie->addSegment(new DateSegment(new DateTimeImmutable('2012-01-01'), 10));
		$serie->addSegment(new DateSegment(new DateTimeImmutable('2012-02-01'), 4));
		$serie->addSegment(new DateSegment(new DateTimeImmutable('2012-03-01'), 8));
		$chart->addSerie($serie);
		
		$serie = new DateSerie(DateSerie::LINE, 'Costs', 'red');
		$serie->addSegment(new DateSegment(new DateTimeImmutable('2012-01-01'), 2));
		$serie->addSegment(new DateSegment(new DateTimeImmutable('2012-02-01'), 9));
		$serie->addSegment(new DateSegment(new DateTimeImmutable('2012-03-01'), 5));
		$chart->addSerie($serie);
		
		$serie = new DateSerie(DateSerie::AREA_LINE, 'Balance', 'blue');
		$serie->addSegment(new DateSegment(new DateTimeImmutable('2012-01-01'), 8));
		$serie->addSegment(new DateSegment(new DateTimeImmutable('2012-02-01'), -5));
		$serie->addSegment(new DateSegment(new DateTimeImmutable('2012-03-01'), 3));
		$chart->addSerie($serie);
		
		$this->template->bigchart = $chart;		




		////////////////////////////////////////////////
		
		
		
		$chart = new Chart();

		$serie = new Serie(Serie::LINE, 'Serie 1', 'red');
		$serie->addSegment(new Segment(5, 10));
		$serie->addSegment(new Segment(6, 4));
		$serie->addSegment(new Segment(2, 8));
		$chart->addSerie($serie);
		
		$serie = new Serie(Serie::LINE, 'Serie 2');
		$serie->addSegment(new Segment(2, 8));
		$serie->addSegment(new Segment(4, 6));
		$serie->addSegment(new Segment(8, 5));
		$serie->addSegment(new Segment(7, 7));
		$chart->addSerie($serie);

		$serie = new Serie(Serie::LINE, 'Kuba');
		$serie->addSegment(new Segment(2, 8));
		$serie->addSegment(new Segment(4, 6));

		$chart->addSerie($serie);		
		
		$this->template->basicchart = $chart;	
		
		
		
		
		
		////////////////////////////////////////////////
		// Pie  RAW

		$pie = new PieChart();
		// $pie->enableRatioLabel(); // Show percents instead of absolute values
		$pie->setValueSuffix(' pcs');
		// $pie->addSegment(new PieSegment('Item 1', 5));
		// $pie->addSegment(new PieSegment('Item 2', 8));
		// $pie->addSegment(new PieSegment('Item 3', 2));
		$pie->enableRaw();
		$pie->addRaw(array(array("Item 1", 5), array("Item 2",8), array("Item 3",2)));
		$this->template->pieRAW = $pie;		

		//////////////////////////////////////////////////
		// Pie

		$pie = new PieChart();
		$pie->enableRatioLabel(); // Show percents instead of absolute values
		$pie->setValueSuffix(' pcs');
		$pie->addSegment(new PieSegment('Item 1', 5));
		$pie->addSegment(new PieSegment('Item 2', 8));
		$pie->addSegment(new PieSegment('Item 3', 2));
		$this->template->pie = $pie;		

		///////////////////////////////////////////////////
		// Donut RAW
		$chart = new DonutChart();
		$chart->setTitle("15");
		$chart->setValueSuffix(' pcs');
		
		$chart->enableRaw();
		$chart->addRaw(array(array("Item 1", 5), array("Item 2",8), array("Item 3",2)));
		$this->template->donutRAW = $chart;

		////////////////////////////////////////////////////
		// Donut
		$chart = new DonutChart();
		$chart->setTitle("15");
		$chart->setValueSuffix(' pcs');
		// $chart->enableRatioLabel(); // Show percents instead of absolute values
		$chart->addSegment(new DonutSegment('Item 1', 5));
		$chart->addSegment(new DonutSegment('Item 2', 8));
		$chart->addSegment(new DonutSegment('Item 3', 2));
		$this->template->donut = $chart;

		$basic = new BasicChart();
		$basic->addRaw(array(array("a", 11,20,22, 18, 35, 16), array("b",20,10,21, 0, 14, 8)));
		$this->template->basicChart = $basic;




	}	

	public function actionReset()
	{
		// $this->thisSensorManager->resetDB($sName);
		$events = $this->thissSensorManager->getAllEvents("Pletacka1", '2020-05-05 6:57:00', '2020-05-05 9:00:00');
		// foreach($events as $event)
		// {
		// 	dump($event);
		// }

	}

}

