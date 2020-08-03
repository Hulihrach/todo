<?php declare(strict_types=1);

namespace App\Presenters;

use App\Exceptions\DuplicateException;
use App\Model\UserManager;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;


final class HomepagePresenter extends Presenter
{
	/** @var UserManager @inject */
	public $userManager;

	/**
	 * @throws AbortException
	 */
	public function startup(): void
	{
		parent::startup();

		if ($this->getAction() !== 'logout' && $this->getUser()->isLoggedIn()) {
			$this->redirect('Todo:default');
		}
	}

	/**
	 * @throws AbortException
	 */
	public function actionLogout(): void
	{
		$this->getUser()->logout(true);

		$this->flashMessage('You have been signed out.', 'green');
		$this->redirect('default');
	}

	public function createComponentLoginForm(): Form
	{
		$form = $this->getBaseForm();

		$form->onSuccess[] = [$this, 'performLogin'];

		return $form;
	}

	/**
	 * @param Form $form
	 * @throws AbortException
	 */
	public function performLogin(Form $form): void
	{
		$values = $form->getValues();

		try {
			$this->user->login($values->email, $values->password);
		} catch (AuthenticationException $e) {
			$this->flashMessage($e->getMessage(), 'red');
			$this->redirect('this');
		}

		$this->flashMessage('You have been signed in.', 'green');
		$this->redirect('Todo:default');
	}

	public function createComponentRegisterForm(): Form
	{
		$form = $this->getBaseForm();

		$form->addPassword('password_check', 'Password check')
			->setRequired("The field 'Password check' must be filled.")
			->setHtmlAttribute('placeholder', 'Password confirmation');

		$form->onSuccess[] = [$this, 'performRegister'];

		return $form;
	}

	/**
	 * @param Form $form
	 * @throws AbortException
	 */
	public function performRegister(Form $form): void
	{
		$values = $form->getValues();

		try {
			$this->userManager->register($values->email, $values->password);
		} catch (DuplicateException $e) {
			$this->flashMessage($e->getMessage(), 'red');
			$this->redirect('this');
		}

		$this->flashMessage('An account was successfully created. Please, sign in now.', 'green');
		$this->redirect('default');
	}

	private function getBaseForm(): Form
	{
		$form = new Form();

		$form->addText('email', 'E-Mail')
			->setHtmlAttribute('placeholder', 'Email address')
			->setRequired("The field 'Email address' must be filled")
			->setHtmlType('email')
			->addRule(Form::EMAIL, 'E-Mail is not valid');

		$form->addPassword('password', 'Password')
			->setHtmlAttribute('placeholder', 'Password')
			->setRequired("The field 'Password' must be filled")
			->addRule(Form::MIN_LENGTH, 'Password must be at least 6 characters long', 6);

		return $form;
	}

}
