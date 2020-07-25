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
				Route::VALUE => 'default',
				Route::FILTER_TABLE => [
					// řetězec v URL => akce presenteru
					'editovat' => 'edit',
					'info' => 'sensor',
					'smazat' => 'delete',
					'test' => 'test',
				]
			]
		]);

		$router[] = new Route('sign/<action>', [
			'presenter' => 'Core:Sign',
			'action' => 'in'
		]);

		$router[] = new Route('test/<action>[/<name>]', [
			'presenter' => 'Core:Test',
			'action' => 'default'
		]);		


		$router[] = new Route('<presenter>/<action>[/<name>]', 'Core:Homepage:default');
		return $router;
	}
}