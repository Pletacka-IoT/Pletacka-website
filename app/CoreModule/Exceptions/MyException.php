<?php

namespace App\Exceptions;

use Exception;
use Nette;
use Nette\Http\IResponse;



/**
 * The exception that indicates client error with HTTP code 4xx.
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
 * Exception for unexisting table
 */
class TableNotExist extends MyException
{
	protected $message = 'Table with this name is not exist';
}


/**
 * Exception for unexisting sensor
 */
class SensorNotExist extends MyException
{
	protected $message = 'Sensor with this name is not exist';
}

class ExampleShow extends MyException
{
	protected $message = "My custom exception";
}
