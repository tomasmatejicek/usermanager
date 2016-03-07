<?php

namespace Mepatek\TaskManager\Repository;



interface IRepository
{
	/** find by ID **/
	public function find($id);

	/** find by $values **/
	public function findBy(array $values, $order, $limit, $offset);

	/** find by $values **/
	public function countBy(array $values);

}