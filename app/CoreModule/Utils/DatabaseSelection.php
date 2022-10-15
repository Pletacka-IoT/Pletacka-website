<?php

declare(strict_types=1);

namespace App\Utils;

use Nette;

/**
 * @brief
 */
class DatabaseSelection
{
	use Nette\SmartObject;

	public $t_stop = 0;
	public $t_work = 0;
	public $t_all = 0;
	public $c_FINISHED = 0;
	public $c_STOP = 0;
    
}