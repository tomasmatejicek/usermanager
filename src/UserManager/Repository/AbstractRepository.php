<?php

namespace Mepatek\UserManager\Repository;

use Nette\Object,
	Mepatek\UserManager\IMapper;

abstract class AbstractRepository extends Object implements IRepository
{

	/** IMapper */
	protected $mapper;

	/**
	* Delete
	*
	* @param mixed $id
	* @return boolean
	*/
	public function delete($id)
	{
		return $this->mapper->delete($id);
	}

	/**
	* Find items by values
	*
	* @param array $values
	* @param array $order
	* @param int $limit
	* @param int $offset
	* @return array of items
	*/
	public function findBy(array $values, $order=null, $limit=null, $offset=null)
	{
		return $this->mapper->findBy($values, $order, $limit, $offset);
	}

	/**
	 * Count items by $values
	 *
	 * @param array $values
	 * @return integer
	 */
	public function countBy(array $values)
	{
		return $this->mapper->countBy($values);
	}


}
