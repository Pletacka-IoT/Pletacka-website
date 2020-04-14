<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Presenter;
use App\Forms\FormFactory;
use Nette\Database\Context;





/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Presenter 
{
	protected $formFactory;
	
	public final function injectFormFactory(FormFactory $formFactory)
	{
		$this->formFactory = $formFactory;
	}
	
	protected function startup()
	{
		parent::startup();
		if (!$this->getUser()->isAllowed($this->getName(), $this->getAction())) {
			$this->flashMessage('Nejsi přihlášený nebo nemáš dostatečná oprávnění.', "error");
			$this->redirect('Sign:in');
		}
	}
}
