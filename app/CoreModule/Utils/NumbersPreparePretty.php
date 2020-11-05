<?php

declare(strict_types=1);

namespace App\Utils;

use Nette;

/**
 * @brief Class create pretty output in specific format
 */
class NumbersPreparePretty
{
	use Nette\SmartObject;

	public $number = "";
	public $allTime = 0;
	public $stopTime = 0;
	public $workTime = 0;
	public $rating = 0;

	public function __construct(int $number, int $allTime = 0, int $stopTime = 0, int $workTime = 0, int $rating = 0)
	{
		$this->number = $number;
		$this->allTime = $allTime;
		$this->stopTime = $stopTime;
		$this->workTime = $workTime;
		$this->rating = $rating;
	}


}