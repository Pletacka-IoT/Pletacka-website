<?php

declare(strict_types=1);

namespace App\CoreModule\Presenters;
use Nette\Security\User;
use Nette\HTTP\IResponse;
use Tracy\Debugger;
use App\Presenters\BasePresenter;


/**
 * @brief What???
 */
final class ConnectPresenter extends BasePresenter
{

	// To test paste to browser URL/connect?token=eb0f21d63594e58d6b9995a7d2ac156c => output "Connect"
	public function renderDefault(): void
	{
		Debugger::$showBar = false;

        if ($this->request->parameters["token"] == 'eb0f21d63594e58d6b9995a7d2ac156c') {
			echo "Connect";
		}
        else
        {
        	echo "ERROR connect, try paste to browser URL/connect?token=eb0f21d63594e58d6b9995a7d2ac156c";
        }


	}

}
