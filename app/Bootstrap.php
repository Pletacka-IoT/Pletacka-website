<?php

declare(strict_types=1);

namespace App;

use Nette\Configurator;

/**
 * @brief Configuration file
 */
class Bootstrap
{
	public static function boot(): Configurator
	{
		$configurator = new Configurator;

		$configurator->setDebugMode(array("192.168.0.112", "192.168.0.134", "25.86.125.145", "192.168.0.114", "192.168.0.15",  "192.168.0.141", "25.58.99.187", "192.168.0.154")); // enable for your remote IP
//		$configurator->setDebugMode(false);
		$configurator->enableTracy(__DIR__ . '/../log');

		$configurator->setTimeZone('Europe/Prague');
		$configurator->setTempDirectory(__DIR__ . '/../temp');

		$configurator->createRobotLoader()
			->addDirectory(__DIR__)
			->register();

		$configurator
			->addConfig(__DIR__ . '/config/common.neon')
			->addConfig(__DIR__ . '/config/local.neon');

		return $configurator;
	}


	public static function bootForTests(): Configurator
	{
		$configurator = self::boot();
		\Tester\Environment::setup();
		return $configurator;
	}
}
