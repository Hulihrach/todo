<?php declare(strict_types=1);

namespace App\Components;

use App\Entities\TodoList;
use App\Model\TodoManager;
use Doctrine\ORM\ORMException;
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

	public function handleArchive(int $id): void
	{
		$list = $this->todoManager->findList($id);

		if (!$list) {

		}

		if ($list->getUser() !== $this->getPresenter()->getUser()->getId()) {

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
