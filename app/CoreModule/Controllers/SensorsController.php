<?php declare(strict_types = 1);

namespace App\Controllers;

use Apitte\Core\Annotation\Controller\ControllerPath;
use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\Model\DatabaseManager;

/**
 * @ControllerPath("/sensors")
 */
final class SensorsController extends BaseV1Controller
{
	private $databaseManager;
	
	public function __construct(DatabaseManager $databaseManager)
	{
		$this->databaseManager = $databaseManager;
	}


	/**
	 * @Path("/")
	 * @Method("GET")
	 */
	public function sensors(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		$aSensors = array();
        $sensors = $this->databaseManager->getSensors();
        foreach($sensors as $sensor)
        {
            $aSensors[] = array('number'=>$sensor->number, 'name'=>$sensor->name, 'description'=>$sensor->description);
        }

        //$xout = array('sensors'=>$aSensors);
		return $response->writeJsonBody($aSensors);
	}

	/**
	 * @Path("/number/{number}")
	 * @Method("GET")
	 */
	public function sensorsNumber(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		$number = $request->getParameter('number');
		if($this->databaseManager->sensorIsExist($number, 'number'))
		{
			$sensor = $this->databaseManager->getSensorsNumber($number);
			return $response->writeJsonBody(array('number'=>$sensor->number, 'name'=>$sensor->name, 'description'=>$sensor->description));
		}
		else
		{
			$error = [
				'status' => 'error',
				'code' => 404,
				'message' => 'Sensor with number '. $number .' does not exist.',
			];
			return $response->writeJsonBody($error);
		}
		

	}



	/**
	 * @Path("/name/{name}")
	 * @Method("GET")
	 */
	public function sensorsName(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		
		$name = $request->getParameter('name');
		if($this->databaseManager->sensorIsExist($name, 'name'))
		{
			$sensor = $this->databaseManager->getSensorsName($name);
			return $response->writeJsonBody(array('number'=>$sensor->number, 'name'=>$sensor->name, 'description'=>$sensor->description));
		}
		else
		{
			$error = [
				'status' => 'error',
				'code' => 404,
				'message' => 'Sensor with name '. $name .' does not exist.',
			];
			return $response->writeJsonBody($error);
		}		
		
		$sensor = $this->databaseManager->getSensorsName($request->getParameter('name'));
		return $response->writeJsonBody(array('number'=>$sensor->number, 'name'=>$sensor->name, 'description'=>$sensor->description));
	}	


	/**
	 * @Path("/ping")
	 * @Method("GET")
	 */
	public function scalar(): string
	{
		return 'pong';
	}

}
