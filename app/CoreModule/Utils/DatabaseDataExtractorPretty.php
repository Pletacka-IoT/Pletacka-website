<?php

declare(strict_types=1);

namespace App\Utils;

use Nette;
use Nette\Utils\DateTime;

/**
 * @brief Class create pretty output in specific format
 * @property int $number
 * @property bool $status
 * @property string $msg
 * @property string $workShift
 * @property int $allTime
 * @property int $stopTime
 * @property int $workTime
 * @property int $finishedCount
 * @property int $stopCount
 * @property DateTime $from
 * @property DateTime $to
 * @property bool $stopThere
 */
class DatabaseDataExtractorPretty
{
	use Nette\SmartObject;

	private $number = -555;
	private $status = false;
	private $msg = "";
	private $workShift = "";
	private $allTime = 0;
	private $stopTime = 0;
	private $workTime = 0;
	private $finishedCount = 0;
	private $stopCount = 0;
	private $from;
	private $to;
	private $stopThere = false;

	public function __construct(int $number = -555, bool $status = false, string $msg = "")
	{
		$this->number = $number;
		$this->status = $status;
		$this->msg = $msg;
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
	 * @return bool
	 */
	protected function getStatus(): bool
	{
		return $this->status;
	}

	/**
	 * @param bool $status
	 */
	protected function setStatus(bool $status): void
	{
		$this->status = $status;
	}

	/**
	 * @return string
	 */public function getMsg(): string
	{
		return $this->msg;
	}


	/**
    * @param string $msg
    */public function setMsg(string $msg): void
	{
		$this->msg = $msg;
	}


	/**
	 * @return string
	 */
	public function getWorkShift(): string
	{
		return $this->workShift;
	}

	/**
	 * @param string $workShift
	 */
	public function setWorkShift(string $workShift): void
	{
		$this->workShift = $workShift;
	}

	/**
	 * @return int
	 */
	protected function getAllTime(): int
	{
		return $this->allTime;
	}

	/**
	 * @param int $allTime
	 */
	protected function setAllTime(int $allTime): void
	{
		$this->allTime = $allTime;
	}

	/**
	 * @return int
	 */
	protected function getStopTime(): int
	{
		return $this->stopTime;
	}

	/**
	 * @param int $stopTime
	 */
	protected function setStopTime(int $stopTime): void
	{
		$this->stopTime = $stopTime;
	}

	/**
	 * @return int
	 */
	protected function getWorkTime(): int
	{
		return $this->workTime;
	}

	/**
	 * @param int $workTime
	 */
	protected function setWorkTime(int $workTime): void
	{
		$this->workTime = $workTime;
	}

	/**
	 * @return int
	 */
	protected function getFinishedCount(): int
	{
		return $this->finishedCount;
	}

	/**
	 * @param int $finishedCount
	 */
	protected function setFinishedCount(int $finishedCount): void
	{
		$this->finishedCount = $finishedCount;
	}

	/**
	 * @return int
	 */
	protected function getStopCount(): int
	{
		return $this->stopCount;
	}

	/**
	 * @param int $stopCount
	 */
	protected function setStopCount(int $stopCount): void
	{
		$this->stopCount = $stopCount;
	}

	/**
	 * @return mixed
	 */
	protected function getFrom()
	{
		return $this->from;
	}

	/**
	 * @param mixed $from
	 */
	protected function setFrom($from): void
	{
		$this->from = $from;
	}

	/**
	 * @return mixed
	 */
	protected function getTo()
	{
		return $this->to;
	}

	/**
	 * @param mixed $to
	 */
	protected function setTo($to): void
	{
		$this->to = $to;
	}

	/**
	 * @return bool
	 */
	public function isStopThere(): bool
	{
		return $this->stopThere;
	}

	/**
	 * @param bool $stopThere
	 */
	public function setStopThere(bool $stopThere): void
	{
		$this->stopThere = $stopThere;
	}


}