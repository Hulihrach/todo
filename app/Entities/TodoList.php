<?php declare(strict_types=1);

namespace App\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class TodoList
{
	use TIdentifier;

	/**
	 * @var Collection|TodoListItem[]
	 * @ORM\OneToMany(targetEntity="TodoListItem", mappedBy="list")
	 */
	private $items;

	/**
	 * @var string
	 * @ORM\Column(type="string")
	 */
	private $name;

	/**
	 * @var User
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="todoLists")
	 */
	private $user;

	/**
	 * @var bool
	 * @ORM\Column(type="boolean")
	 */
	private $archived;

	public function __construct(User $user, string $name)
	{
		$this->user = $user;
		$this->name = $name;
		$this->archived = false;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name): void
	{
		$this->name = $name;
	}

	public function getUser(): User
	{
		return $this->user;
	}

	public function setUser(User $user): void
	{
		$this->user = $user;
	}

	public function isArchived(): bool
	{
		return $this->archived;
	}

	public function setArchived(bool $archived): void
	{
		$this->archived = $archived;
	}

	public function getItems(): Collection
	{
		return $this->items;
	}

	public function getActiveItems(): Collection
	{
		if (empty($this->items)) {
			return new ArrayCollection();
		}

		return $this->items->filter(function (TodoListItem $item) {
			return !$item->isDone();
		});
	}

	public function getInactiveItems(): Collection
	{
		if (empty($this->items)) {
			return new ArrayCollection();
		}

		return $this->items->filter(function (TodoListItem $item) {
			return $item->isDone();
		});
	}

}
