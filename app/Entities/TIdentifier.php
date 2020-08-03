<?php declare(strict_types=1);

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

trait TIdentifier
{
	/**
	 * @var int
	 * @ORM\Id
	 * @ORM\Column (type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	public function getId(): int
	{
		return $this->id;
	}

}
