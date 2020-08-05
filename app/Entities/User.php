<?php declare(strict_types=1);

namespace App\Entities;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table
 * @ORM\Entity
 */
class User
{
	use TIdentifier,
		TCreatedAt;

	/**
	 * @var string
	 * @ORM\Column(type="string", nullable=false, unique=true)
	 */
	private $email;

	/**
	 * @var string
	 * @ORM\Column (type="string", nullable=false)
	 */
	private $password;

	/**
	 * @var Collection|TodoList[]
	 * @ORM\OneToMany(targetEntity="TodoList", mappedBy="user")
	 */
	private $todoLists;

	public function __construct(string $email, string $password)
	{
		$this->email = $email;
		$this->password = $password;
	}

	public function getEmail(): string
	{
		return $this->email;
	}

	public function setEmail(string $email): void
	{
		$this->email = $email;
	}

	public function getPassword(): string
	{
		return $this->password;
	}

	public function setPassword(string $password): void
	{
		$this->password = $password;
	}

	public function getTodoLists(): ?Collection
	{
		return $this->todoLists;
	}

}
