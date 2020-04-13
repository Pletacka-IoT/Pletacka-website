<?php

declare(strict_types=1);

namespace App\CoreModule\Presenters;
use Nette\Security\User;
use App\Presenters\BasePresenter;



final class HomepagePresenter extends BasePresenter
{
	protected $user;
	
	public function __construct(User $user)
	{
		$this->user = $user;
	}
	
	public function renderDefault(): void
	{
		$this->template->iden = "";

		if($this->user->isInRole('admin')) { // je uživatel v roli admina?
			$this->template->test = 'Admin';
			$this->template->iden = $this->user->getIdentity()->username;
		}
		else
		{
			$this->template->test = 'Nekdo jiny';
			$this->template->iden = $this->user->getIdentity()->role;
		}
	}
}
