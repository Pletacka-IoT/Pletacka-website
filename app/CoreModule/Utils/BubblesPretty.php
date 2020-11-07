<?php

declare(strict_types=1);

namespace App\Utils;

use Nette;

/**
 * @brief Class create pretty output in specific format
 */
class BubblesPretty
{
	use Nette\SmartObject;

	public $state = "";
	public $value = 0;
	public $class = "";

	public function __construct(string $state, string $value = "", string $class = "bubble-off")
	{
		$this->state = $state;
		$this->value = $value;
		$this->class = $class;
	}

    
}