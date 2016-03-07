<?php

namespace Mepatek\UserManager\Mapper;

interface IMapper
{
	/** save entity **/
	public function save(&$entity);

	/** delete entity **/
	public function delete($id);

	/** find by ID **/
	public function find($id);

	/** find by $values **/
	public function findBy(array $values, $order, $limit, $offset);

	/** count by $values **/
	public function countBy(array $values);

	/** find first by $values **/
	public function findOneBy(array $values, $order);

}
