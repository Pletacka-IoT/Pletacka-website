<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Error;
use App\Exceptions\MyException;
use Nette\Application\UI\Presenter;

final class ErrorAppPresenter extends BasePresenter
{
	// public function startup(): void
	// {
	// 	parent::startup();
	// 	if (!$this->getRequest()->isMethod(Nette\Application\Request::FORWARD)) {
	// 		$this->error();
	// 	}
	// }


	public function renderDefault(/*MyException $exception, */$message): void
	{
		// echo "Fungujsse->".$message;
		$file = __DIR__ . "/templates/Error/ErrorApp.latte";
		$this->template->setFile($file);
		$this->template->message = $message;
		
	}
}



