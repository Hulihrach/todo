<?php declare(strict_types=1);

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;
use Nette\Utils\DateTime;

/**
 * @ORM\Entity
 */
class TodoListItem
{
	use TIdentifier,
		TCreatedAt;

	/**
	 * @var TodoList
	 * @ORM\ManyToOne(targetEntity="TodoList", inversedBy="items")
	 */
	private $list;

	/**
	 * @var string
	 * @ORM\Column(type="string")
	 */
	private $text;

	/**
	 * @var bool
	 * @ORM\Column(type="boolean")
	 */
	private $done = false;

	public function __construct(TodoList $list, string $text)
	{
		$this->list = $list;
		$this->text = $text;
		$this->createdAt = new DateTime();
	}

	public function getList(): TodoList
	{
		return $this->list;
	}

	public function setList(TodoList $list): void
	{
		$this->list = $list;
	}

	public function getText(): string
	{
		return $this->text;
	}

	public function setText(string $text): void
	{
		$this->text = $text;
	}

	public function isDone(): bool
	{
		return $this->done;
	}

	public function setDone(bool $done): void
	{
		$this->done = $done;
	}

}
