<?php declare(strict_types=1);

namespace App\Presenters;

use App\Components\TodoListControl;
use App\Components\TodoListItemControl;
use App\Entities\TodoList;
use App\Model\TodoManager;
use Doctrine\ORM\ORMException;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Utils\Paginator;

class TodoPresenter extends Presenter
{

	/** @var TodoManager @inject */
	public $todoManager;

	public function renderDefault(int $page = 1): void
	{
		try {
			$listsCount = $this->todoManager->getListsCount($this->getUser()->getId());
		} catch (ORMException $e) {
			$this->flashMessage('Failed to fetch necessary data from the database', 'red');
			$listsCount = 0;
		}

		$paginator = $this->createPaginator($page, $listsCount);

		$this->template->lists = $this->todoManager->findUsersLists($this->getUser()->getId(), $paginator->getLength(), $paginator->getOffset());
		$this->template->paginator = $paginator;
	}

	/**
	 * @param int $id
	 * @param int $page
	 * @throws AbortException
	 */
	public function renderDetail(int $id, int $page = 1): void
	{
		$list = $this->todoManager->findList($id);

		if (!$this->checkExistenceAndAccess($list)) {
			$this->redirect('default');
		}

		try {
			$todosCount = $this->todoManager->getListTodoCount($list);
		} catch (ORMException $e) {
			$this->flashMessage('Failed to fetch necessary data from the database', 'red');
			$todosCount = 0;
		}

		$paginator = $this->createPaginator($page, $todosCount);

		$todos = $this->todoManager->findListTodos($list, $paginator->getLength(), $paginator->getOffset());

		$this->template->list = $list;
		$this->template->todos = $todos;
		$this->template->paginator = $paginator;
	}

	public function createComponentTodoList(): TodoListControl
	{
		return new TodoListControl($this->todoManager);
	}

	public function createComponentTodoListItem(): TodoListItemControl
	{
		return new TodoListItemControl($this->todoManager);
	}

	public function createComponentCreateTodoList(): Form
	{
		$form = new Form;

		$form->addText('name', 'Name');
		$form->addSubmit('submit', 'Create a new list');

		$form->onSuccess[] = [$this, 'createTodoList'];

		return $form;
	}

	/**
	 * @param Form $form
	 * @throws AbortException
	 */
	public function createTodoList(Form $form): void
	{
		$values = $form->getValues();

		try {
			$this->todoManager->createTodoList($this->getUser()->getId(), $values->name);
		} catch (ORMException $e) {
			$this->flashMessage('An error occurred when establishing connection to the database', 'red');
			$this->redirect('this');
		}

		$this->flashMessage("TODO list '{$values->name}' successfully created", 'green');
	}

	public function createComponentAddTask(): Form
	{
		$form = new Form;

		$form->addText('text', 'Text of todo');
		$form->addSubmit('submit', 'Add a task');

		$form->onSuccess[] = [$this, 'addTask'];

		return $form;
	}

	/**
	 * @param Form $form
	 * @throws AbortException
	 */
	public function addTask(Form $form): void
	{
		$values = $form->getValues();

		$listId = (int) $this->getParameter('id');
		$list = $this->todoManager->findList($listId);

		if (!$this->checkExistenceAndAccess($list)) {
			$this->redirect('default');
		}

		try {
			$this->todoManager->createTodoListItem($list, $values->text);
		} catch (ORMException $e) {
			$this->flashMessage('An error occurred when establishing connection to the database', 'red');
			$this->redirect('this');
		}

		$this->flashMessage("TODO item successfully added", 'green');
	}

	private function createPaginator(int $page, int $maxCount): Paginator
	{
		$paginator = new Paginator();
		$paginator->setItemCount($maxCount);
		$paginator->setItemsPerPage(10);
		$paginator->setPage($page);

		return $paginator;
	}

	private function checkExistenceAndAccess(?TodoList $list): bool
	{
		return $list &&
			$list->getUser()->getId() === $this->getUser()->getId();
	}

}
