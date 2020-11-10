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

	public function __construct(int $number)
	{
		$this->number = $number;
	}


}