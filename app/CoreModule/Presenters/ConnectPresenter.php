<?php

declare(strict_types=1);

namespace App\CoreModule\Presenters;
use Nette\Security\User;
use Nette\HTTP\IResponse;
use Tracy\Debugger;
use App\Presenters\BasePresenter;



final class ConnectPresenter extends BasePresenter
{

	
	public function renderDefault(): void
	{
		Debugger::$showBar = false;
		$request = $this->getHttpRequest();
		echo "cau";
        if ($request->getHeader('token') == 'eb0f21d63594e58d6b9995a7d2ac156c') {
			echo "Uspech";
			//$this->error('Invalid or missing access token.', IResponse::S403_FORBIDDEN);
		}

	}

}
