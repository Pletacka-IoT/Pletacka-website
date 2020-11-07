<?php

declare(strict_types=1);

namespace App\Utils;

use Nette;

/**
 * @brief Class create pretty output in specific format
 */
class DatabaseSelectionPretty
{
	use Nette\SmartObject;

	/**
	 * @var int
	 */
	public $number = 1;
	/**
	 * @var string
	 */
	public $workShift = "";
	/**
	 * @var int
	 */
	public $t_stop = 0;
	/**
	 * @var int
	 */
	public $t_work = 0;
	/**
	 * @var int
	 */
	public $t_all = 0;
	/**
	 * @var int
	 */
	public $c_FINISHED = 0;
	/**
	 * @var int
	 */
	public $c_STOP;

	public function __construct(int $number, string $workShift = "", int $t_stop = 0, int $t_work = 0, int $t_all = 0, int $c_FINISHED = 0, int $c_STOP = 0)
	{

		$this->number = $number;
		$this->workShift = $workShift;
		$this->t_stop = $t_stop;
		$this->t_work = $t_work;
		$this->t_all = $t_all;
		$this->c_FINISHED = $c_FINISHED;
		$this->c_STOP = $c_STOP;
	}


}