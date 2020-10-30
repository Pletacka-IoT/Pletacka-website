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
            $aSensors[] = array('number'=>$sensor->number, 'description'=>$sensor->description);
        }

        //$xout = array('sensors'=>$aSensors);
		return $response
            ->writeJsonBody($aSensors)
            ->withStatus(ApiResponse::S200_OK);
	}

	/**
	 * @Path("/{number}")
	 * @Method("GET")
	 * @RequestParameters({
	 *      @RequestParameter(name="number", type="int", description="Sensor number")
	 * })
	 */
	public function sensorsNumber(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		$number = $request->getParameter('number');
		if($this->sensorsManager->sensorIsExist($number, 'number'))
		{
			$sensor = $this->sensorsManager->getSensorsNumber($number);
			return $response
                ->writeJsonBody(array('number'=>$sensor->number, 'description'=>$sensor->description))
                ->withStatus(ApiResponse::S200_OK);
		}
		else
		{
			return $response
                ->writeBody('Error -> Sensor with number '. $number .' does not exist.')
                ->withStatus(ApiResponse::S400_BAD_REQUEST);
		}


		

	}


	
//
//	/**
//	 * @Path("/create")
//	 * @Method("POST")
//	 */
//	public function create(ApiRequest $request, ApiResponse $response): ApiResponse
//	{
//		$post = $request->getJsonBody();
//		$ret = $this->sensorsManager->addNewSensor($post['number'], $post['description']);
//		if($ret->state)
//		{
//			return $response
//				->writeJsonBody(array("state"=>$ret->state, "msg"=>$ret->msg))
//				->withStatus(ApiResponse::S200_OK);
//
//		}
//		else
//		{
//			return $response
//				->writeJsonBody(array("state"=>$ret->state, "msg"=>$ret->msg))
//				->withStatus(ApiResponse::S400_BAD_REQUEST);
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
