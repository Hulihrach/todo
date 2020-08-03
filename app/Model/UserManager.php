<?php declare(strict_types=1);

namespace App\Model;

use App\Entities\User;
use App\Exceptions\DuplicateException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMException;
use Nette\Security\Passwords;
use Nette\SmartObject;
use RuntimeException;

class UserManager
{
	use SmartObject;

	/** @var Passwords */
	private $passwords;

	/** @var EntityManager */
	public $em;

	/** @var EntityRepository  */
	private $userRepository;

	public function __construct(EntityManagerInterface $em, Passwords $passwords)
	{
		$this->passwords = $passwords;
		$this->em = $em;
		$this->userRepository = $em->getRepository(User::class);
	}

	/**
	 * @param string $email
	 * @param string $password
	 * @throws DuplicateException
	 */
	public function register(string $email, string $password): void
	{
		$this->checkDuplicate($email);

		$password = $this->passwords->hash($password);

		$user = new User($email, $password);

		try {
			$this->em->persist($user);
			$this->em->flush();
		} catch (ORMException $e) {
			throw new RuntimeException('An error occurred when establishing connection to the database');
		}
	}

	/**
	 * @param string $email
	 * @throws DuplicateException
	 */
	private function checkDuplicate(string $email): void
	{
		$users = $this->userRepository->findBy(['email' => $email]);

		if (!empty($users)) {
			throw new DuplicateException('E-Mail is already taken');
		}
	}
}