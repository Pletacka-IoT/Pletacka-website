<?php



declare(strict_types=1);

namespace App\Utils;

use Nette;



class Pretty
{
	use Nette\SmartObject;

    public static function return($state, $main, $englishMsg, $czechMsg)
    {
        $x = array($state, $main, $englishMsg, $czechMsg);
        return ($x);
    }

	public static function fromParts(int $year)
	{
		$s = sprintf('%04d- ', $year, );

		return new static($s);
	}    

    public function xreturn($state = true, $main, $englishMsg = "", $czechMsg = "" )
    {
        return new static(array($state, $main, $englishMsg, $czechMsg));
    }    

	/**
	 * DateTime object factory.
	 * @param  string|int|\DateTimeInterface  $time
	 * @return static
	 */
	public static function from($time)
	{

    }
    
}



/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

// declare(strict_types=1);

// namespace App\Utils;
// use Nette;


// /**
//  * DateTime.
//  */
// class Pretty extends \DateTime implements \JsonSerializable
// {
// 	use Nette\SmartObject;

// 	// /** minute in seconds */
// 	// public const MINUTE = 60;

// 	// /** hour in seconds */
// 	// public const HOUR = 60 * self::MINUTE;

// 	// /** day in seconds */
// 	// public const DAY = 24 * self::HOUR;

// 	// /** week in seconds */
// 	// public const WEEK = 7 * self::DAY;

// 	// /** average month in seconds */
// 	// public const MONTH = 2629800;

// 	// /** average year in seconds */
// 	// public const YEAR = 31557600;


// 	// /**
// 	//  * DateTime object factory.
// 	//  * @param  string|int|\DateTimeInterface  $time
// 	//  * @return static
// 	//  */
// 	// public static function from($time)
// 	// {
// 	// 	if ($time instanceof \DateTimeInterface) {
// 	// 		return new static($time->format('Y-m-d H:i:s.u'), $time->getTimezone());

// 	// 	} elseif (is_numeric($time)) {
// 	// 		if ($time <= self::YEAR) {
// 	// 			$time += time();
// 	// 		}
// 	// 		return (new static('@' . $time))->setTimezone(new \DateTimeZone(date_default_timezone_get()));

// 	// 	} else { // textual or null
// 	// 		return new static($time);
// 	// 	}
// 	// }


// 	/**
// 	 * Creates DateTime object.
// 	 * @return static
// 	 */
// 	public static function fromParts(int $year, int $month, int $day, int $hour = 0, int $minute = 0, float $second = 0.0)
// 	{
// 		$s = sprintf('%04d-%02d-%02d %02d:%02d:%02.5f', $year, $month, $day, $hour, $minute, $second);
// 		// if (!checkdate($month, $day, $year) || $hour < 0 || $hour > 23 || $minute < 0 || $minute > 59 || $second < 0 || $second >= 60) {
// 		// 	throw new Nette\InvalidArgumentException("Invalid date '$s'");
// 		// }
// 		return new static($s);
// 	}


// 	/**
// 	 * Returns new DateTime object formatted according to the specified format.
// 	 * @param  string  $format  The format the $time parameter should be in
// 	 * @param  string  $time
// 	 * @param  string|\DateTimeZone  $timezone (default timezone is used if null is passed)
// 	 * @return static|false
// 	 */



// 	/**
// 	 * Returns JSON representation in ISO 8601 (used by JavaScript).
// 	 */
// 	public function jsonSerialize(): string
// 	{
// 		return $this->format('c');
// 	}



// }
