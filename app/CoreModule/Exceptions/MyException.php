<?php

namespace App\Exceptions;

use Exception;
use Nette;
use Nette\Http\IResponse;



/**
 * @brief The exception that indicates client error with HTTP code 4xx.
 */
class MyException extends \Exception
{
	/** @var int */
	protected $code = IResponse::S409_CONFLICT;


	public function __construct(string $thhpMessage = '', int $httpCode = 0, \Throwable $previous = null)
	{
		parent::__construct($thhpMessage ?:$this->message, $httpCode ?: $this->code, $previous);
	}


	public function getHttpCode(): int
	{
		return $this->code;
	}

	public function getHttpMessage(): int
	{
		return $this->message;
	}
}


/**
 * @brief Exception for unexisting table
 */
class ERROR extends MyException
{
	protected $message = 'Unspecificed ERROR';
}


/**
 * @brief Exception for unexisting table - Settings
 */
class SettingsNotExist extends MyException
{
	protected $message = 'Please create table "settings" in DB';
}


/**
 * @brief Exception for unexisting table
 */
class TableNotExist extends MyException
{
	protected $message = 'Table with this name does not exist';
}




/**
 * @brief Exception for unexisting sensor
 */
class SensorNotExist extends MyException
{
	protected $message = 'Sensor with this name does not exist';
}
