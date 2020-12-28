<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Presenter;
use App\Forms\FormFactory;
use Nette\Database\Context;
use App\CoreModule\Model\SensorsManager;
use Latte;





/**
 * @brief Base presenter for all application presenters.
 */
abstract class BasePresenter extends Presenter 
{
	protected $formFactory;
	/**
	 * @var SensorsManager
	 */
	private $sensorsManager;


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
		{
			$this->template->time = date("m/d/Y h:i:s",time());
		}

//	    $ret = $this->sensorsManager->importantTablesAreExist();
//    	if($ret == null)
//	    {
//	    	$this->error("ERROR");
//	    }
		

		$this->redrawAll();


	}	
	
	public final function injectFormFactory(FormFactory $formFactory)
	{
		$this->formFactory = $formFactory;
	}

	
	
	protected function startup()
	{
		parent::startup();

//		$ret = $this->sensorsManager->importantTablesAreExist();
//		if($ret == null)
//		{
//			$this->error("ERROR");
//		}

		if (!$this->getUser()->isAllowed($this->getName(), $this->getAction())) {
			$this->flashMessage('Nejsi přihlášený nebo nemáš dostatečná oprávnění.', "error");
			$this->redirect('Sign:in', array('backlink' => $this->storeRequest()));
		}
	}

}
