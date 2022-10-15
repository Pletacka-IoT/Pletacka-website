<?php declare(strict_types=1);

namespace App\Tests;

use Nette;
use Tester;
use Tester\Assert;
use App\Utils\DatabaseDataExtractorPretty;

require __DIR__ . '/../bootstrap.php';

class DatabaseDataExtractorPrettyTest extends Tester\TestCase
{

	public function testUtil()
	{
		$util = new DatabaseDataExtractorPretty(33, true, "OK");
		Assert::same(33, $util->number);
		Assert::same(true, $util->status);
		Assert::same("OK", $util->msg);
	}

	public function testUtilAdd()
	{
		$util = new DatabaseDataExtractorPretty(33);
		$util->stopCount = 10;
		$util->stopTimeAvg = 10;
		$util->workTime = 25;

		$toAdd = new DatabaseDataExtractorPretty(22);
		$toAdd->stopCount = 5;
		$toAdd->stopTimeAvg = 20;

		$util->add($toAdd);

		Assert::same(-555, $util->number);
		Assert::same(false, $util->status);
		Assert::same(15, $util->stopCount);
		Assert::same(15, $util->stopTimeAvg);
		Assert::same(25, $util->workTime);
	}

}

$test = new DatabaseDataExtractorPrettyTest();
$test->run();