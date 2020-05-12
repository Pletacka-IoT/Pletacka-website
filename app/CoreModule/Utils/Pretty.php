<?php

declare(strict_types=1);

namespace App\Utils;

use Nette;

/**
 * Class create pretty output in specific format
 */
class Pretty
{
	use Nette\SmartObject;

	/**
	 * Method create pretty output in specific format
	 *
	 * @param bool $state
	 * @param mix $main
	 * @param string $englishMsg
	 * @param string $czechMsg
	 * @return void
	 */	
    public static function return($state, $main = "", $englishMsg = "", $czechMsg = "")
    {
        $x = array($state, $main, $englishMsg, $czechMsg);
        return ($x);
    }
    
}