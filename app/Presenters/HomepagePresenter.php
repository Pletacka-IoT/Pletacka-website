<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;


final class HomepagePresenter extends Nette\Application\UI\Presenter
{

    private $database;

	public function __construct(Nette\Database\Connection $database)
	{
		$this->database = $database;
    }

    public function renderDefault() : void
    {
        $rows = $this->database->fetchAll('SELECT * FROM settings');
        print_r($rows); 

        $this->template->settings = $rows[0];
    }
    

}
