<?php

namespace Mepatek\UserManager\Repository;

use Mepatek\Repository\AbstractRepository;

use Mepatek\Mapper\IMapper,
	Nette\Utils\Datetime,
	Mepatek\UserManager\Entity\User;

/**
 * Class UserRepository
 * @package Mepatek\UserManager\Repository
 */
class UserRepository extends AbstractRepository
{

	/**
	 * Constructor
	 *
	 * @param IMapper $mapper
	 */
	public function __construct(IMapper $mapper)
	{
		$this->mapper = $mapper;
	}

	/**
	 * Save
	 *
	 * @param User $item
	 *
	 * @return boolean
	 */
	public function save(User &$item)
	{
		return $this->mapper->save($item);
	}


	/**
	 * Delete User
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
		return $this->mapper->delete((int)$id);
	}

	/**
	 * Permanently delete User
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function deletePermanently($id)
	{
		return $this->mapper->deletePermanently((int)$id);
	}

	/**
	 * Find by id
	 *
	 * @param integer $id
	 *
	 * @return User
	 */
	public function find($id)
	{
		return $this->mapper->find((int)$id);
	}

	/**
	 * Find first item by $values (key=>value)
	 *
	 * @param array $values
	 * @param array $order Order => column=>ASC/DESC
	 *
	 * @return User
	 */
	public function findOneBy(array $values, $order = null)
	{
		return $this->mapper->findOneBy($values, $order);
	}


	/**
	 * Get password hash for user
	 *
	 * @param User $item
	 *
	 * @return string|null
	 */
	public function getPassword(User $item)
	{
		return $this->mapper->getPassword($item);
	}

	/**
	 * Change password for user with $id
	 * Reset PwToken*
	 * False if not find or not change
	 *
	 * @param integer $id
	 * @param string  $newHashPassword
	 *
	 * @return boolean
	 */
	public function changePassword($id, $newHashPassword)
	{
		return $this->mapper->changePassword($id, $newHashPassword);
	}

	/**
	 * Reset and return password token
	 *
	 * @param User     $item
	 * @param DateTime $tokenExpire
	 *
	 * @return string new password token
	 */
	public function resetPasswordToken(User $item, DateTime $tokenExpire)
	{
		return $this->mapper->resetPasswordToken($item, $tokenExpire);
	}

	/**
	 * Find user by token
	 *
	 * @param $token
	 *
	 * @return User|null
	 */
	public function findUserByToken($token)
	{
		return $this->mapper->findUserByToken($token);
	}

}
