<?php declare(strict_types=1);

namespace App\Security;

use App\Entities\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;

final class Authenticator implements IAuthenticator
{
	private const ROLE_DEFAULT = 'default';

	/** @var EntityManager */
	private $em;

	/** @var Passwords */
	private $passwords;

	public function __construct(EntityManagerInterface $em, Passwords $passwords)
	{
		$this->em = $em;
		$this->passwords = $passwords;
	}

	function authenticate(array $credentials): IIdentity
	{
		[$identifier, $password] = $credentials;

		$user = $this->em->getRepository(User::class)->findOneBy(['email' => $identifier]);

		if (!$user) {
			throw new AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);
		}

		if (!$this->passwords->verify($password, $user->getPassword())) {
			throw new AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);
		}

		return new Identity($user->getId(), [self::ROLE_DEFAULT], [
			'email' => $user->getEmail(),
		]);
	}
}