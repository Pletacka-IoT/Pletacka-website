<?php

declare(strict_types=1);

namespace App\Utils;

use Nette;

/**
 * @brief Class create pretty output in specific format
 */
class Pretty
{
	use Nette\SmartObject;

	public $state;
	public $main="";
	public $msg="";

	public function __construct(bool $state, $main = "", $msg = "")
	{
		$this->state = $state;
		$this->main = $main;
		$this->msg = $msg;
	}

	/**
	 * Method create pretty output in specific format
	 *
	 * @param bool $state
	 * @param string $main
	 * @param string $Msg
	 * @return array
	 */
    public static function return(bool $state, $main = "", $Msg = "")
    {
        $x = array($state, $main, $Msg);
        return ($x);
    }
    
}