<?php

namespace Mepatek\UserManager\Mapper;

/**
 * Interface IMapper
 * @package Mepatek\UserManager\Mapper
 */
interface IMapper
{
	/**
	 * Save entity
	 *
	 * @param mixed $entity
	 *
	 * @return boolean
	 */
	public function save(&$entity);

	/**
	 * Delete entity
	 *
	 * @param mixed $id
	 *
	 * @return boolean
	 */
	public function delete($id);

	/**
	 * Find by ID
	 *
	 * @param mixed $id
	 *
	 * @return mixed
	 */
	public function find($id);

	/**
	 * Find by $values
	 *
	 * @param array   $values
	 * @param array   $order Order => column=>ASC/DESC
	 * @param integer $limit Limit count
	 * @param integer $offset Limit offset
	 *
	 * @return array
	 */
	public function findBy(array $values, $order, $limit, $offset);

	/**
	 * Count entities by $values
	 *
	 * @param array $values
	 *
	 * @return integer
	 */
	public function countBy(array $values);

	/**
	 * Find first entity by $values
	 *
	 * @param array $values
	 * @param array $order Order => column=>ASC/DESC
	 *
	 * @return mixed
	 */
	public function findOneBy(array $values, $order);

}
