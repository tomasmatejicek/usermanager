<?php
namespace Mepatek\UserManager\Entity;

/**
 * Class Role
 * @package Mepatek\UserManager\Entity
 */
class Role extends AbstractEntity
{
	/** @var string 30 */
	protected $role;
	/** @var string 100 */
	protected $name;
	/** @var string */
	protected $description;
	/** @var string|false - indicated if entity loaded from repository (=loaded role) or entity is new (false) */
	private $loadedRole = false;

	/**
	 * @return string|boolean
	 */
	public function getLoadedRole()
	{
		return $this->loadedRole;
	}

	/**
	 * @param string $loadedRole
	 */
	public function setLoadedRole($loadedRole)
	{
		// only once set string
		$this->loadedRole = (string)$loadedRole;
	}

	/**
	 * @return string
	 */
	public function getRole()
	{
		return $this->role;
	}

	/**
	 * @param string $role
	 */
	public function setRole($role)
	{
		$this->role = $this->StringTruncate($role, 30);
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $this->StringTruncate($name, 100);
	}

	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @param string $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}

}