<?php declare(strict_types = 1);

namespace App\CoreModule\Controllers;

use Apitte\Core\Annotation\Controller\ControllerPath;
use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\Annotation\Controller\RequestParameters;
use Apitte\Core\Annotation\Controller\RequestParameter;

use App\CoreModule\Model\SensorsManager;
use App\CoreModule\Model\ThisSensorManager;

use Apitte\Core\Exception\Api\MessageException;

/**
 * ThisSensor API class for selected sensor - extend BaseV1Controller
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
     * Add sensor event
     * @Path("/add-event/{number}/{state}")
     * @Method("GET")
     * @RequestParameters({
     *      @RequestParameter(name="number", type="int", description="Sensor number"),
     *      @RequestParameter(name="state", type="string", description="Sensor state")
     * })
     */
	public function add_event(ApiRequest $request, ApiResponse $response): ApiResponse
	{

	    $number = $request->getParameter('number');
		$state = $request->getParameter('state');

		if(!($state == 'FINISHED' || $state == 'STOP' || $state == 'REWORK' || $state == 'ON' || $state == 'OFF'))
        {
            return $response
                ->writeBody("Error -> Invalid state (". $state . ")")
                ->withStatus(ApiResponse::S400_BAD_REQUEST);
        }

		$ret = $this->thisSensorManager->addEvent($number, $state);
		if($ret == true)
		{
            return $response
                ->writeBody("OK -> ".$number." -> ".$state)
                ->withStatus(ApiResponse::S200_OK);
		}

        return $response
            ->writeBody("Error ->".$ret[2])
            ->withStatus(ApiResponse::S400_BAD_REQUEST);
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
