<?php

namespace Mepatek\UserManager\Repository;

use Mepatek\UserManager\Mapper\IMapper,
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
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
		return $this->mapper->delete((int)$id);
	}

	/**
	 * Find by id
	 *
	 * @param integer $id
	 *
	 * @return Role
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
	 * @return Role
	 */
	public function findOneBy(array $values, $order = null)
	{
		return $this->mapper->findOneBy($values, $order);
	}

}
