<?php declare(strict_types = 1);

namespace App\CoreModule\Controllers;

use Apitte\Core\Annotation\Controller\ControllerPath;
use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\CoreModule\Model\SensorManager;
use App\CoreModule\Model\ThisSensorManager;

use Apitte\Core\Exception\Api\MessageException;

/**
 * @ControllerPath("/thisSensor")
 */
final class ThisSensorController extends BaseV1Controller
{
	private $sensorManager;
	private $language;

	
	private $thisSensorManager;
	
	public function __construct(SensorManager $sensorManager, ThisSensorManager $thisSensorManager)
	{
		$this->sensorManager = $sensorManager;
		$this->thisSensorManager = $thisSensorManager;
		$this->language = $this->sensorManager->getAPILanguage();
	}

	




	/**
	 * @Path("/add-pletacka1/{state}")
	 * @Method("GET")
	 */
	public function pletac1(ApiRequest $request, ApiResponse $response): string
	{
		$state = $request->getParameter('state');
		$aSensors = array();
		$ret = $this->thisSensorManager->addEvent("Pletacka1", $state);
		if($ret == true)
		{
			return "OK";
		}

		return "Error ->".$ret[2];
	}
	

	
	/**
	 * @Path("/add-event/{name}/{state}")
	 * @Method("GET")
	 */
	public function xyz(ApiRequest $request, ApiResponse $response): string
	{
		$name = $request->getParameter('name');
		$state = $request->getParameter('state');
		$ret = $this->thisSensorManager->addEvent($name, $state);
		if($ret == true)
		{
			return "OK -> ".$name." -> ".$state;
		}

		return "Error ->".$ret[2];
	}	



	/**
	 * @Path("/ping")
	 * @Method("GET")
	 */
	public function scalar(): string
	{
		// return 'pong';
		$ret = $this->thisSensorManager->addEvent("Pletacka1", "work");
		if($ret == true)
		{
			return "OK";
		}

		return "Error ->".$ret[2];
	}


	/**
	 * @Path("/")
	 * @Method("GET")
	 */
	public function scalar1(): string
	{
		// return 'pong';
		$ret = $this->thisSensorManager->addEvent("Pletacka1", "work");
		if($ret == true)
		{
			return "OK";
		}

		return "Error ->".$ret[2];
	}



	


}
