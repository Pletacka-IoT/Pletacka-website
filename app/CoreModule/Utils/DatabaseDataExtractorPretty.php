<?php

declare(strict_types=1);

namespace App\Utils;

use Nette;

/**
 * @brief Class create pretty output in specific format
 * @property bool $status
 * @property int $number
 * @property int $allTime
 * @property int $stopTime
 * @property int $workTime
 * @property int $finishedCount
 * @property int $stopCount
 */
class DatabaseDataExtractorPretty
{
	use Nette\SmartObject;

	private $status = false;
	private $number = 1;
	private $allTime = 0;
	private $stopTime = 0;
	private $workTime = 0;
	private $finishedCount = 0;
	private $stopCount = 0;


	public function __construct(int $number)
	{
		$this->number = $number;
	}

	/**
	 * @return bool
	 */
	public function getStatus(): bool
	{
		return $this->status;
	}

	/**
	 * @param bool $status
	 */
	public function setStatus(bool $status): void
	{
		$this->status = $status;
	}

	/**
	 * @return int
	 */
	public function getNumber(): int
	{
		return $this->number;
	}

	/**
	 * @param int $number
	 */
	public function setNumber(int $number): void
	{
		$this->number = $number;
	}

	/**
	 * @return int
	 */
	public function getAllTime(): int
	{
		return $this->allTime;
	}

	/**
	 * @param int $allTime
	 */
	public function setAllTime(int $allTime): void
	{
		$this->allTime = $allTime;
	}

	/**
	 * @return int
	 */
	public function getStopTime(): int
	{
		return $this->stopTime;
	}

	/**
	 * @param int $stopTime
	 */
	public function setStopTime(int $stopTime): void
	{
		$this->stopTime = $stopTime;
	}

	/**
	 * @return int
	 */
	public function getWorkTime(): int
	{
		return $this->workTime;
	}

	/**
	 * @param int $workTime
	 */
	public function setWorkTime(int $workTime): void
	{
		$this->workTime = $workTime;
	}

	/**
	 * @return int
	 */
	public function getFinishedCount(): int
	{
		return $this->finishedCount;
	}

	/**
	 * @param int $finishedCount
	 */
	public function setFinishedCount(int $finishedCount): void
	{
		$this->finishedCount = $finishedCount;
	}

	/**
	 * @return int
	 */
	public function getStopCount(): int
	{
		return $this->stopCount;
	}

	/**
	 * @param int $stopCount
	 */
	public function setStopCount(int $stopCount): void
	{
		$this->stopCount = $stopCount;
	}


}