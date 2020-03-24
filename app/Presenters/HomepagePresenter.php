<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use App\Model\DatabaseManager;


final class HomepagePresenter extends Nette\Application\UI\Presenter
{

    private $databaseManager;

	public function __construct(DatabaseManager $databaseManager)
	{
		$this->databaseManager = $databaseManager;
    }

    public function renderDefault() : void
    {
        $this->template->settings = $this->databaseManager->getTitleSettings();
        $variab = "Pepik";
        $this->template->var = $variab;
    }
    

}
