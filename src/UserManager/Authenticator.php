<?php

namespace Mepatek\UserManager;

use Nette,
	Nette\Security,
	Nette\Security\IAuthenticator,
	Nette\Utils\DateTime,
	Mepatek\UserManager\Repository\UserRepository,
	Mepatek\UserManager\Entity\User;


/**
 * Users authenticator.
 */
class Authenticator implements IAuthenticator
{
	/** @var UserRepository */
	private $userRepository;

	/**
	 * Authenticator constructor.
	 *
	 * @param UserRepository $userRepository
	 */
	public function __construct(UserRepository $userRepository)
	{
		$this->userRepository = $userRepository;
	}

	/**
	 * Performs an authentication.
	 *
	 * @param array $credentials
	 *
	 * @return Nette\Security\Identity
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($username, $password) = $credentials;
		$user = $this->userRepository->findOneBy(
			[
				"userName" => $username,
			]
		);

		if (!$user) {
			throw new Security\AuthenticationException('Wrong username.', self::IDENTITY_NOT_FOUND);
		} elseif (!Security\Passwords::verify($password, $this->userRepository->getPassword($user))) {
			throw new Security\AuthenticationException('Wrong password.', self::INVALID_CREDENTIAL);
		}

		// update lastLogged
		$user->lastLogged = new DateTime();
		$this->userRepository->save($user);

		return new Security\Identity($user->id, $user->roles, $user);
	}

	/**
	 * Generate token for change password.
	 *
	 * @param string $email
	 *
	 * @return string|false
	 */
	public function resetPasswordToken($email)
	{
		$user = $this->userRepository->findOneBy(["email" => $email]);
		// userExist?
		if ($user) {
			$tokenExpires = new DateTime();
			$tokenExpires->add(new \DateInterval('PT60M'));     // 60 min for expire

			$token = $this->userRepository->resetPasswordToken($user, $tokenExpires);

			return $token ? $token : false;
		} else {
			return false;
		}
	}

	/**
	 * Change password for $token
	 *
	 * @param string $token
	 * @param string $newPassword
	 *
	 * @return boolean
	 */
	public function changePasswordToken($token, $newPassword)
	{
		$user = $this->userRepository->findUserByToken($token);
		if ($user) {
			return $this->changePassword($user->id, $newPassword);
		} else {
			return false;
		}
	}

	/**
	 * Change password and reset tokens.
	 *
	 * @param integer $userId
	 * @param string  $newPassword
	 *
	 * @return boolean
	 */
	public function changePassword($userId, $newPassword)
	{
		return $this->userRepository->changePassword($userId, Nette\Security\Passwords::hash($newPassword));
	}

	/**
	 * Check password length and check password complexity
	 *
	 * @param string  $password
	 * @param integer $minLength Minimum length of chatacter
	 * @param integer $minLevel Minimum level safe of password
	 *
	 * @return int 0 -password is OK, 2 -password is short, 4 -password is not safe, 6 -password is short and not safe
	 */
	public function isPasswordSafe($password, $minLength, $minLevel)
	{
		$passwordLevel = 0;

		if (preg_match('`[A-Z]`', $password)) // at least one big sign
		{
			$passwordLevel++;
		}
		if (preg_match('`[a-z]`', $password)) // at least one small sign
		{
			$passwordLevel++;
		}
		if (preg_match('`[0-9]`', $password)) // at least one digit
		{
			$passwordLevel++;
		}
		if (preg_match('`[-!"#$%&\'()* +,./:;<=>?@\[\] \\\\^_\`{|}~]`', $password)) // at least one special character
		{
			$passwordLevel++;
		}

		$retValue = 0;

		if ($minLength > strlen($password)) {
			$retValue += 2;
		}
		if ($minLevel > $passwordLevel) {
			$retValue += 4;
		}

		return $retValue;
	}

}
