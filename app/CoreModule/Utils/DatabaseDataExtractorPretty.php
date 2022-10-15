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
 * @property string $allTimeStr
 * @property int $stopTime
 * @property string $stopTimeStr
 * @property int $stopTimeAvg
 * @property string $stopTimeAvgStr
 * @property int $workTime
 * @property string $workTimeStr
 * @property int $workTimeAvg
 * @property string $workTimeAvgStr
 * @property int $finishedCount
 * @property int $stopCount
 * @property int $rating
 * @property DateTime $from
 * @property DateTime $to
 * @property bool $stopThere
 */


class DatabaseDataExtractorPretty
{
	use Nette\SmartObject;

	private $number = -555;
	private $status = false;
	private $msg = "-";
	private $workShift = "";
	private $allTime = 0;
	private $allTimeStr = "-";
	private $stopTime = 0;
	private $stopTimeStr = "-";
	private $stopTimeAvg = 0;
	private $stopTimeAvgStr = "-";
	private $workTime = 0;
	private $workTimeStr = "-";
	private $workTimeAvg = 0;
	private $workTimeAvgStr = "-";
	private $finishedCount = 0;
	private $stopCount = 0;
	private $rating = 0;
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
	protected function getNumber(): int
	{
		return $this->number;
	}

	/**
	 * @param int $number
	 */
	protected function setNumber(int $number): void
	{
		$this->number = $number;
	}

	/**
	 * @return string
	 */
	protected function getAllTimeStr(): string
	{
		return $this->allTimeStr;
	}

	/**
	 * @param string $allTimeStr
	 */
	protected function setAllTimeStr(string $allTimeStr): void
	{
		$this->allTimeStr = $allTimeStr;
	}

	/**
	 * @return string
	 */
	protected function getStopTimeStr(): string
	{
		return $this->stopTimeStr;
	}

	/**
	 * @param string $stopTimeStr
	 */
	protected function setStopTimeStr(string $stopTimeStr): void
	{
		$this->stopTimeStr = $stopTimeStr;
	}

	/**
	 * @return string
	 */
	protected function getWorkTimeStr(): string
	{
		return $this->workTimeStr;
	}

	/**
	 * @param string $workTimeStr
	 */
	protected function setWorkTimeStr(string $workTimeStr): void
	{
		$this->workTimeStr = $workTimeStr;
	}

	/**
	 * @return int
	 */
	protected function getRating(): int
	{
		return $this->rating;
	}

	/**
	 * @param int $rating
	 */
	protected function setRating(int $rating): void
	{
		$this->rating = $rating;
	}

	/**
	 * @return int
	 */
	protected function getStopTimeAvg(): int
	{
		return $this->stopTimeAvg;
	}

	/**
	 * @param int $stopTimeAvg
	 */
	protected function setStopTimeAvg(int $stopTimeAvg): void
	{
		$this->stopTimeAvg = $stopTimeAvg;
	}

	/**
	 * @return string
	 */
	protected function getStopTimeAvgStr(): string
	{
		return $this->stopTimeAvgStr;
	}

	/**
	 * @param string $stopTimeAvgStr
	 */
	protected function setStopTimeAvgStr(string $stopTimeAvgStr): void
	{
		$this->stopTimeAvgStr = $stopTimeAvgStr;
	}

	/**
	 * @return int
	 */
	protected function getWorkTimeAvg(): int
	{
		return $this->workTimeAvg;
	}

	/**
	 * @param int $workTimeAvg
	 */
	protected function setWorkTimeAvg(int $workTimeAvg): void
	{
		$this->workTimeAvg = $workTimeAvg;
	}

	/**
	 * @return string
	 */
	protected function getWorkTimeAvgStr(): string
	{
		return $this->workTimeAvgStr;
	}

	/**
	 * @param string $workTimeAvgStr
	 */
	protected function setWorkTimeAvgStr(string $workTimeAvgStr): void
	{
		$this->workTimeAvgStr = $workTimeAvgStr;
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
	 */
	protected function getMsg(): string
	{
		return $this->msg;
	}


	/**
    * @param string $msg
    */
	protected function setMsg(string $msg): void
	{
		$this->msg = $msg;
	}


	/**
	 * @return string
	 */
	protected function getWorkShift(): string
	{
		return $this->workShift;
	}

	/**
	 * @param string $workShift
	 */
	protected function setWorkShift(string $workShift): void
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
	protected function isStopThere(): bool
	{
		return $this->stopThere;
	}

	/**
	 * @param bool $stopThere
	 */
	protected function setStopThere(bool $stopThere): void
	{
		$this->stopThere = $stopThere;
	}

	public function add(DatabaseDataExtractorPretty $var)
	{
		$this->number = -555;
		$this->status = false;
		$this->rating = intval(($var->rating + $this->rating)/2);
		$this->allTimeStr = "-";
		$this->stopTimeStr = "-";
		$this->workTimeStr = "-";
		$this->stopTimeAvgStr = "-";
		$this->workTimeAvgStr = "-";
		if($this->msg == "")
		{
			$this->msg = $var->msg;
		}

		$this->allTime += $var->stopTime;
		$this->stopTime += $var->stopTime;
		$this->stopTimeAvg = ($var->stopTimeAvg + $this->stopTimeAvg)/2;
		$this->workTime += $var->stopTime;
		$this->workTimeAvg = ($var->workTimeAvg + $this->workTimeAvg)/2;
		$this->finishedCount += $var->finishedCount;
		$this->stopCount += $var->stopCount;


	}
	
	


}