<?php declare(strict_types = 1);

namespace App\CoreModule\Controllers;

//use Apitte\Core\Annotation\Controller\ControllerPath;
use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Annotation\Controller\RequestParameters;
use Apitte\Core\Annotation\Controller\RequestParameter;
use Apitte\Core\Annotation\Controller\OpenApi;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

use App\CoreModule\Model\SensorsManager;
use App\CoreModule\Model\ThisSensorManager;
use App\CoreModule\Model\DatabaseSelectionManager;

use Apitte\Core\Exception\Api\MessageException;
use Nette\Utils\DateTime;

/**
 * ThisSensor API class for selected sensor - extend BaseV1Controller
 * @Path("/cron")
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






	/**
	 * @Path("/")
	 * @Method("GET")
	 */
	public function cron(ApiRequest $request, ApiResponse $response): ApiResponse
	{

		return $response
			->writeBody("Cron API is running")
			->withStatus(ApiResponse::S200_OK);
	}

	/**
	 * @Path("/last-hour/{number}")
	 * @Method("GET")
	 * @RequestParameters({
	 *      @RequestParameter(name="number", type="int", description="Sensor number")
	 * })
	 */
	public function lastHourNum(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		/** @var int $id Perfectly valid integer */
		$number = $request->getParameter('number');

		$from = new DateTime("2020-10-05 12:53:20");
		$from->setTime($from->format("H")-1,0 );

		$selection = DatabaseSelectionManager::HOUR;

		$ret = $this->databaseSelectionManager->createSelection(intval($number), $selection, $from);

		if($ret->state)
		{
			return $response
				->writeJsonBody(array("state"=>"OK", "number"=>$number, "selection"=>$selection, "from"=>$from))
				->withStatus(ApiResponse::S200_OK);
		}
		else
		{
			return $response
				->writeJsonBody(array("state"=>"ERROR", "msg"=>$ret->msg))
				->withStatus(ApiResponse::S400_BAD_REQUEST);
		}

		// Return response with error or user
	}

	/**
	 * @Path("/last-hour")
	 * @Method("GET")
	 */
	public function lastHour(ApiRequest $request, ApiResponse $response): ApiResponse
	{


		$from = new DateTime();
		$from->setTime(intval($from->format("H"))-1,0 );
		$selection = DatabaseSelectionManager::HOUR;

		$sensors = $this->sensorsManager->getSensors();
		$ret = $this->databaseSelectionManager->createSelections($sensors, $selection, $from);


		if($ret->state)
		{
			return $response
				->writeJsonBody(array("state"=>$ret->state, "main"=>$ret->main))
				->withStatus(ApiResponse::S200_OK);
		}
		else
		{
			return $response
				->writeJsonBody(array("state"=>$ret->state, "main"=>$ret->main))
				->withStatus(ApiResponse::S400_BAD_REQUEST);
		}
	}



	/**
	 * @Path("/last-day")
	 * @Method("GET")
	 */
	public function lastDay(ApiRequest $request, ApiResponse $response): ApiResponse
	{


		$from = new DateTime();
		$from->setTime(0,0 );
		$from->setDate(intval($from->format("Y")), intval($from->format("m")),intval($from->format("d"))-1);
		$selection = DatabaseSelectionManager::DAY;

		$sensors = $this->sensorsManager->getSensors();
		$ret = $this->databaseSelectionManager->createSelections($sensors, $selection, $from);

		if($ret->state)
		{
			return $response
				->writeJsonBody(array("state"=>$ret->state, "main"=>$ret->main))
				->withStatus(ApiResponse::S200_OK);
		}
		else
		{
			return $response
				->writeJsonBody(array("state"=>$ret->state, "main"=>$ret->main))
				->withStatus(ApiResponse::S400_BAD_REQUEST);
		}
	}



	/**
	 * @Path("/last-month")
	 * @Method("GET")
	 */
	public function lastMonth(ApiRequest $request, ApiResponse $response): ApiResponse
	{


		$from = new DateTime("2020-10-06 12:53:20");
		$from->setTime(0,0 );
		$from->setDate(intval($from->format("Y")), intval($from->format("m"))-1,0);
		$selection = DatabaseSelectionManager::MONTH;

		$sensors = $this->sensorsManager->getSensors();
		$ret = $this->databaseSelectionManager->createSelections($sensors, $selection, $from);

		if($ret->state)
		{
			return $response
				->writeJsonBody(array("state"=>$ret->state, "main"=>$ret->main))
				->withStatus(ApiResponse::S200_OK);
		}
		else
		{
			return $response
				->writeJsonBody(array("state"=>$ret->state, "main"=>$ret->main))
				->withStatus(ApiResponse::S400_BAD_REQUEST);
		}
	}

	/**
	 * @Path("/last-year")
	 * @Method("GET")
	 */
	public function lastYear(ApiRequest $request, ApiResponse $response): ApiResponse
	{


		$from = new DateTime("2020-10-06 12:53:20");
		$from->setTime(0,0 );
		$from->setDate(intval($from->format("Y"))-1, 0,0);
		$selection = DatabaseSelectionManager::YEAR;

		$sensors = $this->sensorsManager->getSensors();
		$ret = $this->databaseSelectionManager->createSelections($sensors, $selection, $from);

		if($ret->state)
		{
			return $response
				->writeJsonBody(array("state"=>$ret->state, "main"=>$ret->main))
				->withStatus(ApiResponse::S200_OK);
		}
		else
		{
			return $response
				->writeJsonBody(array("state"=>$ret->state, "main"=>$ret->main))
				->withStatus(ApiResponse::S400_BAD_REQUEST);
		}
	}





//	/**
//	 * @Path("/day")
//	 * @Method("GET")
//	 */
//	public function detail(ApiRequest $request, ApiResponse $response): ApiResponse
//	{
//		$num
//		return $response
//			->writeBody("Jede-".$id)
//			->withStatus(ApiResponse::S200_OK);
//
//		// Return response with error or user
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
