<?php

namespace Mepatek\UserManager\AuthDrivers;

use Adldap\Models,
	Adldap\Adldap,
	Adldap\Connections\Configuration,
	Mepatek\UserManager\Repository\UserRepository,
	Mepatek\UserManager\Repository\RoleRepository,
	Mepatek\UserManager\Repository\UserActivityRepository,
	Mepatek\UserManager\Entity\User,
	Mepatek\UserManager\Entity\Role,
	Mepatek\UserManager\Entity\UserActivity;

class AdLdapAuthDriver implements IAuthDriver
{
	/** @var Adldap */
	protected $ad;
	/** @var false|true|array true = all users, array = user in group [group1, group2, ...] */
	protected $autoAddNewUsersInGroups;
	/** @var boolean true = auto update roles in authenticate method */
	protected $autoUpdateRole = false;
	/** @var boolean true = auto create role if not exist */
	protected $autoCreateRole = false;
	/** @var array group=>role mapping */
	protected $group2Role;

	/** @var UserRepository */
	protected $userRepository;
	/** @var RoleRepository */
	protected $roleRepository;
	/** @var UserActivityRepository */
	protected $userActivityRepository;

	/**
	 * AdLdapAuthDriver constructor.
	 *
	 * @param array           $config
	 * @param bool            $autoUpdateRole
	 * @param bool            $autoCreateRole
	 * @param null|true|array $autoAddNewUsersInGroups
	 * @param array           $group2Role
	 */
	public function __construct(
		array $config,
		$autoUpdateRole = false,
		$autoCreateRole = false,
		$autoAddNewUsersInGroups = null,
		$group2Role = []
	)
	{
		$this->ad = new Adldap($config);
		$this->autoUpdateRole = $autoUpdateRole;
		$this->autoCreateRole = $autoCreateRole;
		$this->autoAddNewUsersInGroups = $autoAddNewUsersInGroups;
		$this->group2Role = $group2Role;
	}

	/**
	 * Set Up event
	 *
	 * @param UserRepository         $userRepository
	 * @param RoleRepository         $roleRepository
	 * @param UserActivityRepository $userActivityRepository
	 */
	public function setUp(UserRepository $userRepository, RoleRepository $roleRepository, UserActivityRepository $userActivityRepository)
	{
		$this->userRepository = $userRepository;
		$this->roleRepository = $roleRepository;
		$this->userActivityRepository = $userActivityRepository;
	}

	/**
	 * @param string    $username
	 * @param string    $password
	 * @param null|User $user
	 *
	 * @return boolean
	 */
	public function authenticate($username, $password, &$user)
	{
		$authSuccess = false;
		if ($this->ad->authenticate($username, $password, true)) {
			$adUser = $this->ad->users()->find($username);
			$sid = \Adldap\Classes\Utilities::binarySidToText($adUser->getObjectSID());

			if ($user === null and $this->hasAutoAddUser($adUser)) {
				$user = $this->createUserFromAd($adUser);
			}

			if ($user !== null) {
				if ($this->autoUpdateRole) {
					$this->updateRole($user, $adUser);
				}
				$user->addAuthDriver($this->getName(), $sid);
				$authSuccess = true;
			}
		}
		return $authSuccess;
	}

	/**
	 * If autoAddNewUsersInGroups==true or user member of grou in autoAddNewUsersInGroups return true
	 *
	 * @param Models\User $adUser
	 *
	 * @return boolean
	 */
	protected function hasAutoAddUser(Models\User $adUser)
	{
		if ($this->autoAddNewUsersInGroups === true) {
			return true;
		}
		if (!is_array($this->autoAddNewUsersInGroups)) {
			return false;
		}

		foreach ($adUser->getMemberOfNames() as $group) {
			$group = \Adldap\Classes\Utilities::unescape($group);
			if (in_array($group, $this->autoAddNewUsersInGroups, true)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Create User Entity from adUser
	 *
	 * @param Models\User $adUser
	 *
	 * @return null|user
	 */
	protected function createUserFromAd(Models\User $adUser)
	{
		$user = new User();

		$user->fullName = $adUser->getDisplayName();
		$user->userName = $adUser->getAccountName();
		$user->email = $adUser->getEmail();
		$user->phone = $adUser->getTelephoneNumber();
		$user->title = $adUser->getTitle();
		$user->thumbnail = $adUser->getThumbnailEncoded();

		// save user
		if ($this->userRepository->save($user)) {
			$userActivity = new UserActivity();
			$userActivity->userId = $user->id;
			$userActivity->type = "createFromAuthDriver";
			$userActivity->description = "Auto create from " . $this->getName();
			$this->userActivityRepository->save($userActivity);
		} else {
			$user = null;
		}

		return $user;
	}

	/**
	 * Update roles
	 *
	 * @param User        $user
	 * @param Models\User $adUser
	 */
	protected function updateRole(User &$user, Models\User $adUser)
	{
		$memberOf = [];
		foreach ($adUser->getMemberOfNames() as $group) {
			$memberOf[] = \Adldap\Classes\Utilities::unescape($group);
		}
		foreach ($this->group2Role as $group=>$role) {
			if (in_array($group, $memberOf, true)) {
				if ($this->roleExists($role)) {
					$user->addRole($role);
				}
			}
		}
	}


	/**
	 * True if role exists
	 * If not exists and autoCreateRole=true, create it
	 * @param string $roleId
	 * @return boolean
	 */
	protected function roleExists($roleId)
	{
		$role = $this->roleRepository->find($roleId);
		if (!$role and $this->autoCreateRole) {
			$role = new Role();
			$role->role = $roleId;
			if (!$this->roleRepository->save($role)) {
				$role = null;
			}
		}
		return $role ? true : false;
	}

	/**
	 * Get auth driver name (max 30char)
	 * @return string
	 */
	public function getName()
	{
		return "AdLDAP";
	}

	/**
	 * @param string $username
	 * @param string $authId
	 * @param string $newPassword
	 *
	 * @return boolean
	 */
	public function changePassword($username, $authId, $newPassword)
	{
		return false;
	}

	/**
	 * @return boolean
	 */
	public function hasChangePassword()
	{
		return false;
	}

}