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
	protected $disabled;
	/** @var bool */
	protected $deleted;

	/** @var array */
	protected $roles = [];

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


}