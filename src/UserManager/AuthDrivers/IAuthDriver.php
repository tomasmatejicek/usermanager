<?php

namespace Mepatek\UserManager\AuthDrivers;

use Mepatek\UserManager\Entity\User,
	Mepatek\UserManager\Repository\UserRepository,
	Mepatek\UserManager\Repository\RoleRepository,
	Mepatek\UserManager\Repository\UserActivityRepository;

/**
 * Interface IAuthDriver
 *
 * @package Mepatek\UserManager\AuthDrivers
 */
interface IAuthDriver
{
	/**
	 * Set Up event
	 *
	 * @param UserRepository         $userRepository
	 * @param RoleRepository         $roleRepository
	 * @param UserActivityRepository $userActivityRepository
	 */
	public function setUp(UserRepository $userRepository, RoleRepository $roleRepository, UserActivityRepository $userActivityRepository);

	/**
	 * @param string $username
	 * @param string $password
	 * @param User   $user (finded user before authenticate)
	 *
	 * @return boolean
	 */
	public function authenticate($username, $password, &$user);

	/**
	 * Get auth driver name (max 30char)
	 * @return string
	 */
	public function getName();

	/**
	 * @param string $username
	 * @param string $authId
	 * @param string $newPassword
	 *
	 * @return boolean
	 */
	public function changePassword($username, $authId, $newPassword);

	/**
	 * @return boolean
	 */
	public function hasChangePassword();

}