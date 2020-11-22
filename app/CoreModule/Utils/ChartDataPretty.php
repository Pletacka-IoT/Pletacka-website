<?php

declare(strict_types=1);

namespace App\Utils;
use Nette;
use Nette\Utils\DateTime;


/**
 * @brief Class create pretty output in specific format
 * @property int $number
 * @property bool $state
 * @property string $message
 * @property string $workShift
 * @property array $data
 * @property float $min
 * @property float $max
 * @property string $name
 * @property string $suffix
 * @property DateTime $from
 * @property DateTime $to
 * @property string $seriesType
 * @property string $color
 * @property bool $enableTimePrecision
 */
class ChartDataPretty
{
	use Nette\SmartObject;

	private $number = -555;
	private $state = false;
	private $message = "";
	private $workShift = "";
	private $data = array();
	private $min = null;
	private $max = null;
	private $name  = "";
	private $suffix = "";
	private $from;
	private $to;
	private $seriesType = "";
	private $color = "";
	private $enableTimePrecision = false;

	public function __construct(string $name, DateTime $from, DateTime $to, string $suffix = "")
	{
		$this->name = $name;
		$this->suffix = $suffix;
		$this->from = $from;
		$this->to = $to;
	}

	/**
	 * @return array
	 */
	public function getData(): array
	{
		return $this->data;
	}

	/**
	 * @param array $data
	 */
	public function setData(array $data): void
	{
		$this->data = $data;
	}

	/**
	 * @return bool
	 */
	public function isEnableTimePrecision(): bool
	{
		return $this->enableTimePrecision;
	}

	/**
	 * @param bool $enableTimePrecision
	 */
	public function setEnableTimePrecision(bool $enableTimePrecision): void
	{
		$this->enableTimePrecision = $enableTimePrecision;
	}

	/**
	 * @return bool
	 */
	public function isState(): bool
	{
		return $this->state;
	}

	/**
	 * @param bool $state
	 */
	public function setState(bool $state): void
	{
		$this->state = $state;
	}

	/**
	 * @return string
	 */
	public function getMessage(): string
	{
		return $this->message;
	}

	/**
	 * @param string $message
	 */
	public function setMessage(string $message): void
	{
		$this->message = $message;
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
	private function getNumber(): int
	{
		return $this->number;
	}

	/**
	 * @param int $number
	 */
	private function setNumber(int $number): void
	{
		$this->number = $number;
	}

	/**
	 * @return int
	 */
	private function getMin()
	{
		return $this->min;
	}

	/**
	 * @param int $min
	 */
	private function setMin($min): void
	{
		$this->min = $min;
	}

	/**
	 * @return int
	 */
	private function getMax()
	{
		return $this->max;
	}

	/**
	 * @param int $max
	 */
	private function setMax($max): void
	{
		$this->max = $max;
	}

	/**
	 * @return string
	 */
	private function getName(): string
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	private function setName(string $name): void
	{
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	private function getSuffix(): string
	{
		return $this->suffix;
	}

	/**
	 * @param string $suffix
	 */
	private function setSuffix(string $suffix): void
	{
		$this->suffix = $suffix;
	}

	/**
	 * @return string
	 */
	private function getSeriesType(): string
	{
		return $this->seriesType;
	}

	/**
	 * @param string $seriesType
	 */
	private function setSeriesType(string $seriesType): void
	{
		$this->seriesType = $seriesType;
	}

	/**
	 * @return string
	 */
	private function getColor(): string
	{
		return $this->color;
	}

	/**
	 * @param string $color
	 */
	private function setColor(string $color): void
	{
		$this->color = $color;
	}


}