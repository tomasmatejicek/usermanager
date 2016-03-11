<?php

namespace Mepatek\UserManager\Repository;

use Mepatek\Repository\AbstractRepository;

use Mepatek\Mapper\IMapper,
	Mepatek\UserManager\Entity\UserActivity;

/**
 * Class UserActivityRepository
 * @package Mepatek\UserManager\Repository
 */
class UserActivityRepository extends AbstractRepository
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
	 * @param UserActivity $item
	 *
	 * @return boolean
	 */
	public function save(UserActivity &$item)
	{
		return $this->mapper->save($item);
	}


	/**
	 * Delete UserActivity
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
	 * @return UserActivity
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
	 * @return UserActivity
	 */
	public function findOneBy(array $values, $order = null)
	{
		return $this->mapper->findOneBy($values, $order);
	}


}
