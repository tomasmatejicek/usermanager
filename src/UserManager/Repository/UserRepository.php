<?php

namespace Mepatek\UserManager\Repository;

use Mepatek\UserManager\Mapper\IMapper,
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

}
