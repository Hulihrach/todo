<?php declare(strict_types=1);

namespace App\Components;

use App\Entities\TodoList;
use App\Model\TodoManager;
use Doctrine\ORM\ORMException;
use Nette\Application\AbortException;
use Nette\Application\UI\Control;
use Nette\SmartObject;

class TodoListControl extends Control
{
	use SmartObject;

	/** @var TodoManager */
	private $todoManager;

	public function __construct(TodoManager $todoManager)
	{
		$this->todoManager = $todoManager;
	}

	/**
	 * @param int $id
	 * @throws AbortException
	 */
	public function handleArchive(int $id): void
	{
		$list = $this->todoManager->findList($id);

		if (!$list) {
			$this->presenter->redirect('this');
		}

		if ($list->getUser() !== $this->presenter->getUser()->getId()) {
			$this->presenter->redirect('default');
		}

		try {
			$this->todoManager->archiveTodoList($list);
		} catch (ORMException $e) {
			$this->flashMessage('An error occurred when establishing connection to the database', 'red');
			$this->redirect('this');
		}
	}

	public function render(TodoList $list): void
	{
		$this->template->list = $list;
		$this->template->render(__DIR__ . '\\TodoListControl.latte');
	}
}
