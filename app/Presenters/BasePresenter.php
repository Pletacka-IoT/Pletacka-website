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

	protected function redrawAll()
	{
		$this->redrawControl('title');
		$this->redrawControl('header');
		$this->redrawControl('msg1');
		$this->redrawControl('msg2');
		$this->redrawControl('content');
		$this->redrawControl('footer');
	}

	public function handleRedrawAll(): void
	{
		$this->redrawAll();
	}    


    protected function beforeRender()
    {
		parent::beforeRender();
		$this->template->time = date("m/d/Y h:i:s",time());

		$this->redrawAll();


	}	
	
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
