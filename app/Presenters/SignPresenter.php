<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Model\DatabaseManager;
use App\Model\ThisSensorManager;
use Nette\Http\Request;
use Nette\Http\UrlScript;


class SignPresenter extends Nette\Application\UI\Presenter
{


    private $databaseManager;
    private $request;
    private $thisSensorManager;

	public function __construct(DatabaseManager $databaseManager, Nette\Http\Request $request, ThisSensorManager $thisSensorManager)
	{
        $this->databaseManager = $databaseManager;
        $this->request = $request;
        $this->thisSensorManager = $thisSensorManager;
    }
    
    
	protected function createComponentSignInForm(): Form
	{
		$form = new Form;
		$form->addText('username', 'Uživatelské jméno:')
			->setRequired('Prosím vyplňte své uživatelské jméno.');

		$form->addPassword('password', 'Heslo:')
			->setRequired('Prosím vyplňte své heslo.');

		$form->addSubmit('send', 'Přihlásit');

		$form->onSuccess[] = [$this, 'signInFormSucceeded'];
		return $form;
    }
    
    public function signInFormSucceeded(Form $form, \stdClass $values): void
    {
        try {
            $this->getUser()->login($values->username, $values->password);
            $this->redirect('Homepage:');
    
        } catch (Nette\Security\AuthenticationException $e) {
            $form->addError('Nesprávné přihlašovací jméno nebo heslo!!!');
        }
    }


    public function actionOut(): void
    {
        $this->getUser()->logout();
        $this->flashMessage('Odhlášení bylo úspěšné.', "success");
        $this->redirect('Homepage:');
    }
    
    public function renderIn()
    {
        $this->template->settings = $this->databaseManager->getTitleSettings();
    }
    
}
