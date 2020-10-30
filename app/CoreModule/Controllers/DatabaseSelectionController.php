<?php declare(strict_types = 1);

namespace App\CoreModule\Controllers;

use Apitte\Core\Annotation\Controller\ControllerPath;
use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

use App\CoreModule\Model\SensorsManager;
use App\CoreModule\Model\ThisSensorManager;
use App\CoreModule\Model\DatabaseSelectionManager;

use Apitte\Core\Exception\Api\MessageException;
use Nette\Utils\DateTime;

/**
 * ThisSensor API class for selected sensor - extend BaseV1Controller
 * @ControllerPath("/cron")
 */
final class DatabaseSelectionController extends BaseV1Controller
{
	private $sensorsManager;
	private $thisSensorManager;
	/**
	 * @var DatabaseSelectionManager
	 */
	private $databaseSelectionManager;

	public function __construct(SensorsManager $sensorsManager, ThisSensorManager $thisSensorManager, DatabaseSelectionManager $databaseSelectionManager)
	{
		$this->sensorsManager = $sensorsManager;
		$this->thisSensorManager = $thisSensorManager;
		$this->databaseSelectionManager = $databaseSelectionManager;
	}

	



//
//	/**
//	 * @Path("/add-pletacka1/{state}")
//	 * @Method("GET")
//	 */
//	public function pletac1(ApiRequest $request, ApiResponse $response): string
//	{
//		$state = $request->getParameter('state');
//		$aSensors = array();
//		$ret = $this->thisSensorManager->addEvent("Pletacka1", $state);
//		if($ret == true)
//		{
//			return "OK";
//		}
//
//		return "Error ->".$ret[2];
//	}


    /**
     * Run database selection - DAY
     * @Path("/day/{number}")
     * @Method("GET")
     */
	public function genNumStateFrom(ApiRequest $request, ApiResponse $response): ApiResponse
	{

	    $number = $request->getParameter('number');

		$x = new DateTime($from);

		$ret = $this->databaseSelectionManager->createSelection(intval($number), $selection, $x );

		if($ret->state)
		{
			return $response
				->writeBody("OK -> Num:".$number."; Select:".$selection."; From:".$x)
				->withStatus(ApiResponse::S200_OK);
		}
		else
		{
	        return $response
	            ->writeBody("Error ->".$ret->msg)
	            ->withStatus(ApiResponse::S400_BAD_REQUEST);
		}
	}

    /**
     * Add sensor event
     * @Path("/gen/{number}/{selection}/{from}")
     * @Method("GET")
     */
	public function genNumStateFrom(ApiRequest $request, ApiResponse $response): ApiResponse
	{

	    $number = $request->getParameter('number');
		$selection = $request->getParameter('selection');
		$from = $request->getParameter('from');

		$x = new DateTime($from);

		$ret = $this->databaseSelectionManager->createSelection(intval($number), $selection, $x );

		if($ret->state)
		{
			return $response
				->writeBody("OK -> Num:".$number."; Select:".$selection."; From:".$x)
				->withStatus(ApiResponse::S200_OK);
		}
		else
		{
	        return $response
	            ->writeBody("Error ->".$ret->msg)
	            ->withStatus(ApiResponse::S400_BAD_REQUEST);
		}
	}




//	/**
//	 * @Path("/plet")
//	 * @Method("GET")
//	 */
//	public function sensors(ApiRequest $request, ApiResponse $response): ApiResponse
//	{
//		$events = $this->thisSensorManager->getAllEvents("Pletacka1", '2020-05-05 6:57:00', '2020-05-05 7:00:00');
//
//        //$xout = array('sensors'=>$aSensors);
//		return $response->writeJsonBody($events);
//	}


//	/**
//	 * @Path("/")
//	 * @Method("GET")
//	 */
//	public function scalar1(): string
//	{
//		// return 'pong';
//		$ret = $this->thisSensorManager->addEvent("Pletacka1", "work");
//		if($ret == true)
//		{
//			return "OK";
//		}
//
//		return "Error ->".$ret[2];
//	}

    /**
     * @Path("/ping")
     * @Method("GET")
     * @param ApiRequest  $request
     * @param ApiResponse $response
     * @return ApiResponse
     */
	public function ping(ApiRequest $request, ApiResponse $response): ApiResponse
	{
        return $response
            ->writeBody("pong")
            ->withStatus(ApiResponse::S200_OK);
	}


}
