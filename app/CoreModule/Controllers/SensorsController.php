<?php declare(strict_types = 1);

namespace App\Controllers;

use Apitte\Core\Annotation\Controller\ControllerPath;
use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

/**
 * @ControllerPath("/sensors")
 */
final class SensorsController extends BaseV1Controller
{

	/**
	 * @Path("/")
	 * @Method("GET")
	 */
	public function index(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		return $response->writeJsonBody(['hello' => ['world']]);
	}

	/**
	 * @Path("/name{id}")
	 * @Method("GET")
	 */
	public function indexId(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		return $response
			->writeJsonBody(['sensors' => [
				'id' => $request->getParameter('id'),
			]]);
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
