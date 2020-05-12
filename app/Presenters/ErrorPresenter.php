<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\Responses;
use Nette\Http;
use Tracy\ILogger;
use App\Exceptions\MyException;

use App\Exceptions\ExampleShow;
use Error;
use Exception;


final class ErrorPresenter implements Nette\Application\IPresenter
{
	use Nette\SmartObject;

	/** @var ILogger */
	private $logger;


	public function __construct(ILogger $logger)
	{
		$this->logger = $logger;
	}


	/**
	 * @return Nette\Application\IResponse
	 */
	public function run(Nette\Application\Request $request): Nette\Application\IResponse
	{
		$e = $request->getParameter('exception');
		var_dump($e);
		echo("<br><br>");

		if ($e instanceof MyException) {
			echo "MyException2";
			return new Responses\ForwardResponse($request->setPresenterName("ErrorApp")->setParameters(['message' => $e->getMessage()]));
		}	
		
		

		if ($e instanceof Nette\Application\BadRequestException) {
			// $this->logger->log("HTTP code {$e->getCode()}: {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}", 'access');
			[$module, , $sep] = Nette\Application\Helpers::splitName($request->getPresenterName());
			$errorPresenter = $module . $sep . 'Error4xx';
			echo("*".$errorPresenter."*");
			return new Responses\ForwardResponse($request->setPresenterName($errorPresenter));
		}

		$this->logger->log($e, ILogger::EXCEPTION);
		return new Responses\CallbackResponse(function (Http\IRequest $httpRequest, Http\IResponse $httpResponse): void {
			if (preg_match('#^text/html(?:;|$)#', (string) $httpResponse->getHeader('Content-Type'))) {
				require __DIR__ . '/templates/Error/500.phtml';
			}
		});
	}
}
