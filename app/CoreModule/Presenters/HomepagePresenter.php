<?php

declare(strict_types=1);

namespace App\CoreModule\Presenters;
use App\Presenters\BasePresenter;
use App\Model\DatabaseManager;



final class HomepagePresenter extends BasePresenter
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
