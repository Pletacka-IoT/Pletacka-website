<?php

declare(strict_types=1);

namespace App\Forms;

use App\Model;
use Nette;
use Nette\Application\UI\Form;
use Nette\Security\User;

/**
 * @brief Factory for sign-up
 */
final class SignUpFormFactory
{
	use Nette\SmartObject;

	private const PASSWORD_MIN_LENGTH = 7;

	/** @var FormFactory */
	private $factory;

	/** @var Model\UserManager */
	private $userManager;
	private $user;


	public function __construct(FormFactory $factory, Model\UserManager $userManager, User $user)
	{
		$this->factory = $factory;
		$this->userManager = $userManager;
		$this->user = $user;
	}


	public function create(callable $onSuccess): Form
	{
		$form = $this->factory->create();
		$form->addText('username', 'Jméno:')
			->setRequired('Zadejte prosím uživatelské jméno');

		$form->addPassword('password', 'Heslo')
			->setOption('description', sprintf('alespoň %d znaků', self::PASSWORD_MIN_LENGTH))
			->setRequired('Zadejte prosím uživatelské heslo')
			->addRule($form::MIN_LENGTH, null, self::PASSWORD_MIN_LENGTH);

		$form->addSubmit('send', 'Registrovat');

		$form->onSuccess[] = function (Form $form, \stdClass $values) use ($onSuccess): void {
			try {
				$this->userManager->add($values->username, $values->password);
				$this->user->login($values->username, $values->password);

			} catch (Model\DuplicateNameException $e) {
				$form['username']->addError('Uživatelské jméno je již obsazeno');
				return;
			}
			$onSuccess();
		};

		return $form;
	}
}
