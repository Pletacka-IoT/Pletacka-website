<?php declare(strict_types = 1);

namespace App\CoreModule\Controllers;

use Apitte\Core\Annotation\Controller\ControllerPath;
use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\CoreModule\Model\SensorsManager;

use Apitte\Core\Exception\Api\MessageException;

/**
 * Sensor API class - extend BaseV1Controller
 * @ControllerPath("/sensors")
 */
final class SensorsController extends BaseV1Controller
{
	private $sensorsManager;

	private $defaultArticleUrl;
	
	public function __construct(SensorsManager $sensorsManager)
	{
		$this->sensorsManager = $sensorsManager;
	}


	/**
	 * @Path("/")
	 * @Method("GET")
	 */
	public function sensors(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		$aSensors = array();
        $sensors = $this->sensorsManager->getSensors();
        foreach($sensors as $sensor)
        {
            $aSensors[] = array('number'=>$sensor->number, 'name'=>$sensor->name, 'description'=>$sensor->description);
        }

        //$xout = array('sensors'=>$aSensors);
		return $response->writeJsonBody($aSensors);
	}

	/**
	 * @Path("/{number}")
	 * @Method("GET")
	 */
	public function sensorsNumber(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		$number = $request->getParameter('number');
		if($this->sensorsManager->sensorIsExist($number, 'number'))
		{
			$sensor = $this->sensorsManager->getSensorsNumber($number);
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


	
//
//	/**
//	 * @Path("/create")
//	 * @Method("POST")
//	 */
//	public function create(ApiRequest $request): array
//	{
//		$post = $request->getJsonBody();
//		$returnMessage = $this->sensorsManager->addNewSensor($post['number'], $post['name'], $post['description']);
//		if($returnMessage[0])
//		{
//			return ['message'=>$returnMessage[$this->language]];
//
//		}
//		else
//		{
//
//			throw MessageException::create()
//			->withCode(405)
//			->withMessage($returnMessage[$this->language]);
//		}
//	}
//
//		/**
//	 * @Path("/update")
//	 * @Method("PUT")
//	 */
//	public function update(ApiRequest $request): array
//	{
//		$post = $request->getJsonBody();
//		$returnMessage = $this->sensorsManager->editSensor($post['old-name'],$post['number'], $post['name'], $post['description']);
//		if($returnMessage[0])
//		{
//			return ['message'=>$returnMessage[$this->language]];
//		}
//		else
//		{
//
//			throw MessageException::create()
//			->withCode(405)
//			->withMessage($returnMessage[$this->language]);
//		}
//	}
//
//	/**
//	 * @Path("/delete")
//	 * @Method("DELETE")
//	 */
//	public function delete(ApiRequest $request): array
//	{
//		$post = $request->getJsonBody();
//		$returnMessage = $this->sensorsManager->deleteSensor($post['name']);
//		if($returnMessage[0])
//		{
//			return ['message'=>$returnMessage[$this->language], $this->language];
//		}
//		else
//		{
//
//			throw MessageException::create()
//			->withCode(405)
//			->withMessage($returnMessage[$this->language]);
//		}
//	}


	/**
	 * @Path("/ping")
	 * @Method("GET")
	 */
	public function scalar(): string
	{
		return 'pong';
	}



	


}
