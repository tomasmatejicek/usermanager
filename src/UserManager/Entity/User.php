<?php
namespace Mepatek\UserManager\Entity;


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
	protected $roles = array();

	/** @var UserActivity[]|null */
	protected $activities = null;

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
		return $this->disabled;
	}

	/**
	 * @param boolean $disabled
	 */
	public function setDisabled($disabled)
	{
		$this->disabled = (bool)$disabled;
	}

	/**
	 * @return boolean
	 */
	public function getDeleted()
	{
		return $this->deleted;
	}

	/**
	 * @param boolean $deleted
	 */
	public function setDeleted($deleted)
	{
		$this->deleted = (bool)$deleted;
	}

	/**
	 * @return Role[]
	 */
	public function getRoles()
	{
		return $this->roles;
	}

	/**
	 * @param Role[] $roles
	 */
	public function setRoles($roles)
	{
		$this->roles = $roles;
	}

	/**
	 * @return UserActivity[]|null
	 */
	public function getActivities()
	{
		return $this->activities;
	}

	/**
	 * @param UserActivity[]|null $activities
	 */
	public function setActivities($activities)
	{
		$this->activities = $activities;
	}


}