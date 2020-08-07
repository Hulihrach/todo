<?php declare(strict_types=1);

namespace App\Model;

use App\Entities\TodoList;
use App\Entities\TodoListItem;
use App\Entities\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Nette\SmartObject;

class TodoManager
{
	use SmartObject;

	/** @var EntityManager */
	private $em;
	
	/** @var EntityRepository */
	private $todoListRepository,
		$todoListItemRepository,
		$userRepository;


	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
		$this->todoListRepository = $em->getRepository(TodoList::class);
		$this->todoListItemRepository = $em->getRepository(TodoListItem::class);
		$this->userRepository = $em->getRepository(User::class);
	}

	public function findList(int $id): ?TodoList
	{
		return $this->todoListRepository->find($id);
	}

	public function findItem(int $id): ?TodoListItem
	{
		return $this->todoListItemRepository->find($id);
	}

	public function findUsersLists(int $userId, ?int $limit = null, ?int $offset = null): array
	{
		$user = $this->userRepository->find($userId);

		return $this->todoListRepository->findBy(['user' => $user, 'archived' => false], ['id' => 'DESC'], $limit, $offset);
	}

	public function findListTodos(?TodoList $list, int $limit, int $offset): array
	{
		return $this->todoListItemRepository->findBy(['list' => $list, 'done' => false], ['createdAt' => 'DESC'], $limit, $offset);
	}

	/**
	 * @param int $userId
	 * @return int
	 * @throws ORMException
	 */
	public function getListsCount(int $userId): int
	{
		$user = $this->userRepository->find($userId);

		return $this->todoListRepository->createQueryBuilder('list')
			->select('COUNT(list)')
			->where('list.user = :user')
			->andWhere('list.archived = :archived')
			->setParameters([':user' => $user, ':archived' => false])
			->getQuery()->getSingleScalarResult();
	}

	/**
	 * @param TodoList $list
	 * @return int
	 * @throws ORMException
	 */
	public function getListTodoCount(TodoList $list): int
	{
		return $this->todoListItemRepository->createQueryBuilder('item')
			->select('COUNT(item)')
			->where('item.list = :list')
			->andWhere('item.done = :done')
			->setParameters([':list' => $list, ':done' => false])
			->getQuery()->getSingleScalarResult();
	}

	/**
	 * @param int $userId
	 * @param string $name
	 * @throws ORMException
	 */
	public function createTodoList(int $userId, string $name): void
	{
		$user = $this->userRepository->find($userId);
		$list = new TodoList($user, $name);

		$this->save($list);
	}


	/**
	 * @param TodoList $list
	 * @param string $text
	 * @throws ORMException
	 */
	public function createTodoListItem(TodoList $list, string $text): void
	{
		$todo = new TodoListItem($list, $text);

		$this->save($todo);
	}

	/**
	 * @param TodoList $list
	 * @throws ORMException
	 */
	public function archiveTodoList(TodoList $list): void
	{
		$list->setArchived(true);

		$this->save($list);
	}

	/**
	 * @param TodoListItem $todo
	 * @throws ORMException
	 */
	public function archiveTodoListItem(TodoListItem $todo): void
	{
		$todo->setDone(true);

		$this->save($todo);
	}

	/**
	 * @param TodoListItem $todo
	 * @throws ORMException
	 */
	public function removeTodoListItem(TodoListItem $todo): void
	{
		$this->em->remove($todo);

		$this->em->flush();
	}

	/**
	 * @param TodoListItem|null $todo
	 * @param string $text
	 * @throws ORMException
	 */
	public function changeTodoListItemText(TodoListItem $todo, string $text): void
	{
		$todo->setText($text);

		$this->save($todo);
	}

	/**
	 * @param TodoList|TodoListItem $entity
	 * @throws ORMException
	 */
	private function save($entity): void
	{
		$this->em->persist($entity);
		$this->em->flush();
	}

}