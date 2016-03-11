<?php

namespace Mepatek\UserManager\Repository;

use Mepatek\Repository\AbstractRepository;

use Mepatek\Mapper\IMapper,
	Mepatek\UserManager\Entity\Role;

/**
 * Class RoleRepository
 * @package Mepatek\UserManager\Repository
 */
class RoleRepository extends AbstractRepository
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
	 * @param Role $item
	 *
	 * @return boolean
	 */
	public function save(Role &$item)
	{
		return $this->mapper->save($item);
	}


	/**
	 * Delete Role
	 *
	 * @param string $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
		return $this->mapper->delete((string)$id);
	}

	/**
	 * Delete Role permanently
	 *
	 * @param string $id
	 *
	 * @return boolean
	 */
	public function deletePermanently($id)
	{
		return $this->mapper->deletePermanently((string)$id);
	}


	/**
	 * Find by id
	 *
	 * @param string $id
	 *
	 * @return Role
	 */
	public function find($id)
	{
		return $this->mapper->find((string)$id);
	}

	/**
	 * Find first item by $values (key=>value)
	 *
	 * @param array $values
	 * @param array $order Order => column=>ASC/DESC
	 *
	 * @return Role
	 */
	public function findOneBy(array $values, $order = null)
	{
		return $this->mapper->findOneBy($values, $order);
	}

}
