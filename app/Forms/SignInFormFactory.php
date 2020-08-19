<?php

declare(strict_types=1);

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nette\Security\User;

/**
 * @brief Factory for sign-in
 */
final class SignInFormFactory
{
	use Nette\SmartObject;

	/** @var FormFactory */
	private $factory;

	/** @var User */
	private $user;


	public function __construct(FormFactory $factory, User $user)
	{
		$this->factory = $factory;
		$this->user = $user;
	}


	public function create(callable $onSuccess): Form
	{
		$form = $this->factory->create();
		$form->addText('username', 'Jméno:')
			->setRequired('Prosím zadejte jméno');

		$form->addPassword('password', 'Heslo:')
			->setRequired('Prosím zadejte heslo');

		// $form->addCheckbox('remember', 'Keep me signed in');

		$form->addSubmit('send', 'Přihlásit se');

		$form->onSuccess[] = function (Form $form, \stdClass $values) use ($onSuccess): void {
			try {
				$this->user->setExpiration('14 days');
				$this->user->login($values->username, $values->password);
			} catch (Nette\Security\AuthenticationException $e) {
				$form->addError('Jméno nebo heslo bylo zadáno nesprávně');
				return;
			}
			$onSuccess();
		};

		return $form;
	}
}
