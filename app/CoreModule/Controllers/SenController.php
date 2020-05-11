<?php declare(strict_types = 1);

namespace App\CoreModule\Controllers;

use Apitte\Core\Annotation\Controller\ControllerPath;
use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\CoreModule\Model\SensorManager;

use Apitte\Core\Exception\Api\MessageException;

/**
 * @ControllerPath("/sen")
 */
final class SenController extends BaseController
{
	private $sensorManager;
	private $language;

	private $defaultArticleUrl;
	
	public function __construct(SensorManager $sensorManager)
	{
		$this->sensorManager = $sensorManager;
		$this->language = $this->sensorManager->getAPILanguage();
	}


	/**
	 * @Path("/")
	 * @Method("GET")
	 */
	public function sensors(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		$aSensors = array();
        $sensors = $this->sensorManager->getSensors();
        foreach($sensors as $sensor)
        {
            $aSensors[] = array('number'=>$sensor->number, 'name'=>$sensor->name, 'description'=>$sensor->description);
        }

        //$xout = array('sensors'=>$aSensors);
		return $response->writeJsonBody($aSensors);
	}

	/**
	 * @Path("/find-name/{name}")
	 * @Method("GET")
	 */
	public function find(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		$name = $request->getParameter('name');
		$aSensors = array();
        $sensors = $this->sensorManager->findSensorsName($name);
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
		if($this->sensorManager->sensorIsExist($number, 'number'))
		{
			$sensor = $this->sensorManager->getSensorsNumber($number);
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
		if($this->sensorManager->sensorIsExist($name, 'name'))
		{
			$sensor = $this->sensorManager->getSensorsName($name);
			return $response->writeJsonBody(array('number'=>$sensor->number, 'name'=>$sensor->name, 'description'=>$sensor->description));
		}
		else
		{
			throw MessageException::create()
			->withCode(405)
			->withMessage("Sensor with name ". $name . " does not exist.");
		}
	}
	
	
	/**
	 * @Path("/create")
	 * @Method("POST")
	 */
	public function create(ApiRequest $request): array
	{
		$post = $request->getJsonBody();
		$returnMessage = $this->sensorManager->addNewSensor($post['number'], $post['name'], $post['description']);
		if($returnMessage[0])
		{
			return ['message'=>$returnMessage[$this->language]];

		}
		else
		{
			
			throw MessageException::create()
			->withCode(405)
			->withMessage($returnMessage[$this->language]);
		}  
	}	

		/**
	 * @Path("/update")
	 * @Method("PUT")
	 */
	public function update(ApiRequest $request): array
	{
		$post = $request->getJsonBody();
		$returnMessage = $this->sensorManager->editSensor($post['old-name'],$post['number'], $post['name'], $post['description']);
		if($returnMessage[0])
		{
			return ['message'=>$returnMessage[$this->language]];
		}
		else
		{
			
			throw MessageException::create()
			->withCode(405)
			->withMessage($returnMessage[$this->language]);
		}  
	}

	/**
	 * @Path("/delete")
	 * @Method("DELETE")
	 */
	public function delete(ApiRequest $request): array
	{
		$post = $request->getJsonBody();
		$returnMessage = $this->sensorManager->deleteSensor($post['name']);
		if($returnMessage[0])
		{
			return ['message'=>$returnMessage[$this->language], $this->language];
		}
		else
		{
			
			throw MessageException::create()
			->withCode(405)
			->withMessage($returnMessage[$this->language]);
		}  
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
