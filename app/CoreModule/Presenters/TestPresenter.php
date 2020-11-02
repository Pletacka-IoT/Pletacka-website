<?php
declare(strict_types=1);


namespace App\CoreModule\Presenters;

use Nette\Forms\Form;
use Tracy\Debugger;
use Tracy\Dumper;
use App\Presenters\BasePresenter;


use App\CoreModule\Model\SensorsManager;
use App\CoreModule\Model\ThisSensorManager;
use App\CoreModule\Model\WorkShiftManager;
use App\CoreModule\Model\DatabaseSelectionManager;
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



/**
 * @brief Presenter for testing
 */
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
    private $workShiftManager;
    private $databaseSelectionManager;

	public function __construct(
	    SensorsManager $sensorsManager,
        ThisSensorManager $thisSensorManager,
        Request $request,
        SensorsFormFactory $sensorsFormFactory,
        WorkShiftManager $workShiftManager,
        DatabaseSelectionManager $databaseSelectionManager
    )
	{
        
        $this->sensorsManager = $sensorsManager;
        $this->thisSensorManager = $thisSensorManager;
        $this->request = $request;
        $this->sensorsFormFactory = $sensorsFormFactory;
        $this->workShiftManager = $workShiftManager;
        $this->databaseSelectionManager = $databaseSelectionManager;
	}
	

	public function actionDebug($name)
	{
//        for($s = 1; $s<=20; $s++)
//        {
//			for($i = 0; $i<24; $i++)
//	        {
//	            $x = $i;
//	            if($x<10){$x = "0".$x;}
//	            $returnMessage =  $this->databaseSelectionManager->createSelection(17, DatabaseSelectionManager::HOUR, DateTime::from("2020-10-2".$s." ".$x.":02"));
//	        }
//
////			$returnMessage =  $this->databaseSelectionManager->createSelection(17, DatabaseSelectionManager::DAY, DateTime::from("2020-10-2".$s." 07:02"));
////			$returnMessage =  $this->databaseSelectionManager->createSelection(17, DatabaseSelectionManager::MONTH, DateTime::from("2020-10-2".$s." 07:02"));
////			$returnMessage =  $this->databaseSelectionManager->createSelection(17, DatabaseSelectionManager::YEAR, DateTime::from("2020-10-2".$s." 07:02"));
////
//        }
//		echo "Ahoj";
//		for($i = 10; $i<=19; $i++)
//		{
//			$sensors = $this->sensorsManager->getSensors();
////	//
//			dump($this->databaseSelectionManager->createSelections($sensors, DatabaseSelectionManager::HOUR, new DateTime("2020-10-30 ".$i.":02:32")));
////
////		}

////		$this->databaseSelectionManager->createSelection(17, DatabaseSelectionManager::HOUR, DateTime::from("2020-10-29 11:02:32"));
//		for($i = 1; $i<=23; $i++)
//			if($i != 17)
//				$this->sensorsManager->addNewSensor($i, "Pletacka - ".$i);
//				$this->sensorsManager->deleteSensor($i);


//
//        if($returnMessage->state)
//        {
//	        $this->flashMessage($returnMessage->msg, 'success');
//        }
//        else
//        {
//            $this->flashMessage($returnMessage->msg, 'error');
//        }

	}

	public function renderChart(): void
	{
        //////////////////////////////////////////////

        $chartd = new DateChart();
        $chartd->setValueSuffix(' $');
        $chartd->enableTimePrecision();

        //$chart->enableTimePrecision(); // Enable time accurate to seconds

        for($x = 0; $x<80; $x++)
        {
            $serie = new DateSerie(DateSerie::LINE, 'Costs-'.$x, dechex(rand(0x000000, 0xFFFFFF)));
            for($i = 1; $i<=20; $i++)
            {
                $text = '2012-';
                $text .= strval(rand(10,12));
                $text .= '-';
                $text .= strval(rand(10,30));
                $serie->addSegment(new DateSegment(new DateTimeImmutable($text), rand(2,10)));

            }
            $chartd->addSerie($serie);
        }

        $this->template->datechart = $chartd;


        //////////////////////////////////////////////

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
		$chart->setTitle("14");
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

	public function actionTime()
    {

    }


}

