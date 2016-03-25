<?php
namespace Mepatek\UserManager\Entity;

use Mepatek\Entity\AbstractEntity;

/**
 * Class User
 * @package Mepatek\UserManager\Entity
 */
class User extends AbstractEntity
{
	/** @var integer */
	protected $id = null;
	/** @var string 255 */
	protected $fullName;
	/** @var string 50 */
	protected $userName;
	/** @var string 255 */
	protected $email;
	/** @var string 255 */
	protected $phone;
	/** @var \Nette\Utils\DateTime */
	protected $created;
	/** @var \Nette\Utils\DateTime */
	protected $lastLogged;
	/** @var bool */
	protected $disabled = false;
	/** @var bool */
	protected $deleted = false;

	/** @var array */
	protected $roles = [];

	/** @var array authDriverName=>authId */
	protected $authDrivers = [];

	/** @var string */
	protected $authMethod;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setId($id)
	{
		// ONLY if id is not set
		if (!$this->id) {
			$this->id = (int)$id;
		}
	}

	/**
	 * @return string
	 */
	public function getFullName()
	{
		return $this->fullName;
	}

	/**
	 * @param string $fullName
	 */
	public function setFullName($fullName)
	{
		$this->fullName = $this->StringTruncate($fullName, 255);
	}

	/**
	 * @return string
	 */
	public function getUserName()
	{
		return $this->userName;
	}

	/**
	 * @param string $userName
	 */
	public function setUserName($userName)
	{
		$this->userName = $this->StringTruncate($userName, 50);
	}

	/**
	 * @return string
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * @param string $email
	 */
	public function setEmail($email)
	{
		$this->email = $this->StringTruncate($email);
	}

	/**
	 * @return string
	 */
	public function getPhone()
	{
		return $this->phone;
	}

	/**
	 * @param string $phone
	 */
	public function setPhone($phone)
	{
		$this->phone = $this->StringTruncate($phone, 255);
	}

	/**
	 * @return \Nette\Utils\DateTime
	 */
	public function getCreated()
	{
		return $this->created;
	}

	/**
	 * @param \Nette\Utils\DateTime $created
	 */
	public function setCreated($created)
	{
		$this->created = $this->DateTime($created);
	}

	/**
	 * @return \Nette\Utils\DateTime
	 */
	public function getLastLogged()
	{
		return $this->lastLogged;
	}

	/**
	 * @param \Nette\Utils\DateTime $lastLogged
	 */
	public function setLastLogged($lastLogged)
	{
		$this->lastLogged = $this->DateTime($lastLogged);
	}

	/**
	 * @return boolean
	 */
	public function getDisabled()
	{
		return $this->disabled ? true : false;
	}

	/**
	 * @param boolean $disabled
	 */
	public function setDisabled($disabled)
	{
		$this->disabled = $disabled ? true : false;
	}

	/**
	 * @return boolean
	 */
	public function getDeleted()
	{
		return $this->deleted ? true : false;
	}

	/**
	 * @param boolean $deleted
	 */
	public function setDeleted($deleted)
	{
		$this->deleted = $deleted ? true : false;
	}


	/**
	 * @return string
	 */
	public function getAuthMethod()
	{
		return $this->authMethod;
	}

	/**
	 * @param string $authMethod
	 */
	public function setAuthMethod($authMethod)
	{
		$this->authMethod = $authMethod;
	}

	/**
	 * Get role array
	 * @return Role[]
	 */
	public function getRoles()
	{
		return array_values($this->roles);
	}

	/**
	 * @param array $roles
	 */
	public function setRoles(array $roles)
	{
		$this->deleteAllRoles();
		foreach ($roles as $role) {
			$this->addRole($role);
		}
	}

	/**
	 * delete all roles
	 */
	public function deleteAllRoles()
	{
		$this->roles = [];
	}

	/**
	 * Add role
	 *
	 * @param string $role
	 */
	public function addRole($role)
	{
		$this->roles[$role] = $role;
	}

	/**
	 * Delete role
	 *
	 * @param string $role
	 */
	public function deleteRole($role)
	{
		if (isset($this->roles[$role])) {
			unset ($this->roles[$role]);
		}
	}

	/**
	 * Get authDrivers with ID
	 * @return array
	 */
	public function getAuthDrivers()
	{
		return $this->authDrivers;
	}

	/**
	 * @param array $authDrivers
	 */
	public function setAuthDrivers(array $authDrivers)
	{
		$this->deleteAllAuthDrivers();
		foreach ($authDrivers as $authDriver => $authId) {
			$this->addAuthDriver($authDriver, $authId);
		}
	}

	/**
	 * delete all authDrivers
	 */
	public function deleteAllAuthDrivers()
	{
		$this->authDrivers = [];
	}

	/**
	 * Add authDriver
	 *
	 * @param string $authDriver
	 * @param string $authId
	 */
	public function addAuthDriver($authDriver, $authId)
	{
		$this->authDrivers[$authDriver] = $authId;
	}

	/**
	 * Delete authDriver
	 *
	 * @param string $authDriver
	 */
	public function deleteAuthDriver($authDriver)
	{
		if (isset($this->authDrivers[$authDriver])) {
			unset ($this->authDrivers[$authDriver]);
		}
	}

}