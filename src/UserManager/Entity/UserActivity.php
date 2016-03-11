<?php
namespace Mepatek\UserManager\Entity;

use Mepatek\Entity\AbstractEntity;

/**
 * Class UserActivity
 * @package Mepatek\UserManager\Entity
 */
class UserActivity extends AbstractEntity
{

	/** @var integer */
	protected $id = null;
	/** @var integer */
	protected $userId;
	/** @var string 50 */
	protected $ip;
	/** @var string 30 */
	protected $type;
	/** @var \Nette\Utils\DateTime */
	protected $datetime;
	/** @var string */
	protected $description;

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
	 * @return int
	 */
	public function getUserId()
	{
		return $this->userId;
	}

	/**
	 * @param int $userId
	 */
	public function setUserId($userId)
	{
		$this->userId = $userId;
	}

	/**
	 * @return string
	 */
	public function getIp()
	{
		return $this->ip;
	}

	/**
	 * @param string $ip
	 */
	public function setIp($ip)
	{
		$this->ip = $this->StringTruncate($ip, 50);
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param string $type
	 */
	public function setType($type)
	{
		$this->type = $this->StringTruncate($type, 30);
	}

	/**
	 * @return \Nette\Utils\DateTime
	 */
	public function getDatetime()
	{
		return $this->datetime;
	}

	/**
	 * @param \Nette\Utils\DateTime $datetime
	 */
	public function setDatetime($datetime)
	{
		$this->datetime = $this->DateTime($datetime);
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