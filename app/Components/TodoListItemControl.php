<?php declare(strict_types=1);

namespace App\Components;

use App\Entities\TodoListItem;
use App\Model\TodoManager;
use Doctrine\ORM\ORMException;
use Nette\Application\AbortException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\SmartObject;

class TodoListItemControl extends Control
{
	use SmartObject;

	/** @var TodoManager */
	private $todoManager;

	public function __construct(TodoManager $todoManager)
	{
		$this->todoManager = $todoManager;
	}

	public function render(TodoListItem $item): void
	{
		$this->template->item = $item;
		$this->template->render(__DIR__ . '\\TodoListItemControl.latte');
	}

	/**
	 * @param int $id
	 * @throws AbortException
	 */
	public function handleArchive(int $id): void
	{
		$todo = $this->todoManager->findItem($id);

		if (!$this->checkExistenceAndAccess($todo)) {
			$this->presenter->redirect('default');
		}

		try {
			$this->todoManager->archiveTodoListItem($todo);
		} catch (ORMException $e) {
			$this->presenter->flashMessage('An error occurred when establishing connection to the database', 'red');
			$this->redirect('this');
		}

		$this->presenter->flashMessage('Task successfully archived', 'green');
		$this->redirect('this');
	}

	/**
	 * @param int $id
	 * @throws AbortException
	 */
	public function handleRemove(int $id): void
	{
		$todo = $this->todoManager->findItem($id);

		if (!$this->checkExistenceAndAccess($todo)) {
			$this->presenter->redirect('default');
		}

		try {
			$this->todoManager->removeTodoListItem($todo);
		} catch (ORMException $e) {
			$this->presenter->flashMessage('An error occurred when establishing connection to the database', 'red');
			$this->redirect('this');
		}

		$this->presenter->flashMessage('Task successfully removed', 'green');
		$this->redirect('this');
	}

	public function createComponentEditTodoListItem(): Form
	{
		$form = new Form;

		$form->addHidden('id');
		$form->addText('text', 'Text');

		$form->addSubmit('submit', 'Save changes');

		$form->onSuccess[] = [$this, 'editTodoListItem'];

		return $form;
	}

	/**
	 * @param Form $form
	 * @throws AbortException
	 */
	public function editTodoListItem(Form $form): void
	{
		$values = $form->getValues();

		$todo = $this->todoManager->findItem((int) $values->id);

		if (!$this->checkExistenceAndAccess($todo)) {
			$this->presenter->redirect('default');
		}

		try {
			$this->todoManager->changeTodoListItemText($todo, $values->text);
		} catch (ORMException $e) {
			$this->presenter->flashMessage('An error occurred when establishing connection to the database', 'red');
			$this->redirect('this');
		}

		$this->presenter->flashMessage('TODO item edited', 'green');
		$this->redirect('this');
	}

	private function checkExistenceAndAccess(?TodoListItem $todo): bool
	{
		return $todo &&
			$todo->getList()->getUser()->getId() === $this->presenter->getUser()->getId();
	}

}
