<?php declare(strict_types = 1);

namespace App\CoreModule\Controllers;

use Apitte\Core\Annotation\Controller\ControllerPath;
use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\CoreModule\Model\SensorsManager;
use App\CoreModule\Model\ThisSensorManager;

use Apitte\Core\Exception\Api\MessageException;

/**
 * @ControllerPath("/thisSensor")
 */
final class ThisSensorController extends BaseV1Controller
{
	private $sensorsManager;

	
	private $thisSensorManager;
	
	public function __construct(SensorsManager $sensorsManager, ThisSensorManager $thisSensorManager)
	{
		$this->sensorsManager = $sensorsManager;
		$this->thisSensorManager = $thisSensorManager;
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
	 * @Path("/add-event/{number}/{state}")
	 * @Method("GET")
	 */
	public function xyz(ApiRequest $request, ApiResponse $response): string
	{
		$number = $request->getParameter('number');
		$state = $request->getParameter('state');
		$ret = $this->thisSensorManager->addEvent($number, $state);
		if($ret == true)
		{
			return "OK -> ".$number." -> ".$state;
		}

		return "Error ->".$ret[2];
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
	 */
	public function ping(): string
	{
		return 'pong';
	}


}
