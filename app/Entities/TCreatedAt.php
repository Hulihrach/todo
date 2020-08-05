<?php declare(strict_types=1);

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;
use \DateTime;

trait TCreatedAt
{
	/**
	 * @var DateTime
	 * @ORM\Column(type="datetime")
	 */
	private $createdAt;

	public function getCreatedAt(): DateTime
	{
		return $this->createdAt;
	}

	public function setCreatedAt(DateTime $createdAt): void
	{
		$this->createdAt = $createdAt;
	}

}
