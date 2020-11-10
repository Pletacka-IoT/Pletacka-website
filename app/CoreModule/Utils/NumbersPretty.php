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
	public $finishedCount;
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
	/**
	 * @var bool
	 */
	public $state;

	public function __construct(bool $state = false, int $finishedCount = 0, int $rating = 0, int $stopTime = 0, string $stopTimeStr = "", int $workTime = 0, int $allTime = 0)
	{
		$this->finishedCount = $finishedCount;
		$this->rating = $rating;
		$this->stopTime = $stopTime;
		$this->workTime = $workTime;
		$this->allTime = $allTime;
		$this->stopTimeStr = $stopTimeStr;
		$this->state = $state;
	}

	public function divideTimeVariablesByCount(int $num)
	{
		$this->stopTime = intval($this->stopTime/$num);
		$this->workTime = intval($this->workTime/$num);
		$this->allTime = intval($this->allTime/$num);
	}

	public function finishedCountToPairs()
	{
		$this->finishedCount = intval(ceil($this->finishedCount/2));
	}


}