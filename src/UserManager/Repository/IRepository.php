<?php

namespace Mepatek\UserManager\Repository;


/**
 * Interface IRepository
 * @package Mepatek\UserManager\Repository
 */
interface IRepository
{
	/**
	 * Find by ID
	 *
	 * @param $id
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

}