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
	/** @var bool - indicated if entity loaded from repository (true) or is new (false)*/
	private $loaded = false;

	/**
	 * @return boolean
	 */
	public function getLoaded()
	{
		return $this->loaded;
	}

	/**
	 * @param boolean $loaded
	 */
	public function setLoaded($loaded)
	{
		// only once set true
		$this->loaded = ($this->loaded or (bool)$loaded);
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