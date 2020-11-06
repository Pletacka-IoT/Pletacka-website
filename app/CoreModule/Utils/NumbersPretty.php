<?php

declare(strict_types=1);

namespace App\Utils;

use Nette;

/**
 * @brief Class create pretty output in specific format
 */
class NumbersPretty
{
	use Nette\SmartObject;

	/**
	 * @var int
	 */
	public $finished;
	/**
	 * @var int
	 */
	public $stopTime;
	/**
	 * @var int
	 */
	public $rating;
	/**
	 * @var int
	 */
	public $workTime;
	/**
	 * @var int
	 */
	public $allTime;
	/**
	 * @var string
	 */
	public $stopTimeStr;

	public function __construct(int $finished = 0, int $rating = 0, int $stopTime = 0, string $stopTimeStr = "", int $workTime = 0, int $allTime = 0)
	{
		$this->finished = $finished;
		$this->rating = $rating;
		$this->stopTime = $stopTime;
		$this->workTime = $workTime;
		$this->allTime = $allTime;
		$this->stopTimeStr = $stopTimeStr;
	}

	public function divideTimeVariablesByCount(int $num)
	{
		$this->stopTime/=$num;
		$this->workTime/=$num;
		$this->allTime/=$num;
	}

	public function finishedCountToPairs()
	{
		$this->finished = intval($this->finished/2);
	}


}