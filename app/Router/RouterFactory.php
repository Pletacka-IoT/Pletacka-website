<?php

declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;
use Apitte\Presenter\ApiRoute;



/**
 * @brief Factory for routes
 */
final class RouterFactory
{
	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
		$router = new RouteList;
		$router[] = new ApiRoute('api');

		$router[] = new Route('pletacka[/<number>][/<do>]', [
			'presenter' => 'Core:Sensors',
			'action' => "default"
		]);

		$router[] = new Route('senzory/<action>[/<number>]', [
			'presenter' => 'Core:SensorsSettings',
			'action' => [
				// Route::FILTER_STRICT => true,
				Route::VALUE => 'default',
				Route::FILTER_TABLE => [
					// řetězec v URL => akce presenteru
					'upravit' => 'edit',
					'info' => 'info',
					'smazat' => 'delete',
					'test' => 'test',
                    'debug' => 'debug',
                    'pridat' => 'add',
				]
			]
		]);

		$router[] = new Route('smeny/<action>[/<year>][/<week>]', [
			'presenter' => 'Core:WorkShift',
			'action' => [
				// Route::FILTER_STRICT => true,
				Route::VALUE => 'default',
				Route::FILTER_TABLE => [
					'test' => 'test',
				]
			]
		]);

		$router[] = new Route('sign/<action>', [
			'presenter' => 'Core:Sign',
			'action' => 'in',

		]);

		$router[] = new Route('test/<action>[/<number>]', [
			'presenter' => 'Core:Test',
			'action' => 'default'
		]);

		$router[] = new Route('connect', [
			'presenter' => 'Core:Connect',
			'action' => 'default'
		]);


		$router[] = new Route('<presenter>/<action>[/<number>]', 'Core:Homepage:default');
		return $router;
	}
}