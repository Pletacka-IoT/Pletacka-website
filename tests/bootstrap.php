<?php

require_once __DIR__ . '/../vendor/autoload.php';

if ( ! class_exists('Tester\Assert')) {
	echo 'Missing Nette Tester';
	exit(1);
}

Tester\Environment::setup();
date_default_timezone_set('Europe/Prague');

define('TEMP_DIR', __DIR__ . '/temp/' . (isset($_SERVER['argv']) ? md5(serialize($_SERVER['argv'])) : getmypid()));
//Tester\Helpers::purge(TEMP_DIR);
