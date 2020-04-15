<?php

declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;
use Apitte\Presenter\ApiRoute;



final class RouterFactory
{
	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
		$router = new RouteList;
		$router[] = new ApiRoute('api');

		$router[] = new Route('senzory/<action>[/<name>]', [
			'presenter' => 'Core:Sensors',
			'action' => [
				// Route::FILTER_STRICT => true,
				Route::FILTER_TABLE => [
					// řetězec v URL => akce presenteru
					'vse' => 'default',
					'editovat' => 'edit',
					'senzory' => 'sensor',
					'smazat' => 'delete',
					'ping' => 'test',
				]
			]
		]);

		$router[] = new Route('<presenter>/<action>[/<name>]', 'Core:Homepage:default');
		return $router;
	}
}
