<?php

namespace Mepatek\UserManager\Mapper;

use Mepatek\Mapper\AbstractNetteDatabaseMapper,
	Mepatek\Mapper\IMapper;

use Nette,
	Nette\Database\Context,
	Mepatek\UserManager\Entity\Role;

/**
 * Class RoleNetteDatabaseMapper
 * @package Mepatek\UserManager\Mapper
 */
class RoleNetteDatabaseMapper extends AbstractNetteDatabaseMapper implements IMapper
{
	/** @var boolean TRUE - find deleted row */
	private $deleted;

	/**
	 * RoleNetteDatabaseMapper constructor.
	 *
	 * @param Context $database
	 */
	public function __construct(Context $database)
	{
		$this->database = $database;
	}

	/**
	 * Save item
	 *
	 * @param Role $item
	 *
	 * @return boolean
	 */
	public function save(&$item)
	{
		$data = $this->itemToData($item);
		$retSave = false;

		if (!$item->loadedRole) { // new --> insert

			$this->getTable()
				->insert($data);
			$newItem = $this->find($item->role);
			if ($newItem) {
				$item = $newItem;
				$retSave = true;
			}
		} else { // update

			$row = $this->getTable()
				->where("Role", $item->loadedRole)
				->update($data);

			if ($row) {
				$retSave = true;
			}
		}

		return $retSave;
	}

	/**
	 * Item data to array
	 *
	 * @param Role $item
	 *
	 * @return array
	 */
	private function itemToData(Role $item)
	{
		$data = [];

		foreach ($this->mapItemPropertySQLNames() as $property => $columnSql) {
			$data[$columnSql] = $item->$property;
		}

		return $data;
	}

	/**
	 * Get array map of item property vs SQL columns name for Tasks table
	 * @return array
	 */
	protected function mapItemPropertySQLNames()
	{
		return [
			"role"        => "Role",
			"name"        => "RoleName",
			"description" => "Description",
		];
	}

	/**
	 * Get view object
	 * @return \Nette\Database\Table\Selection
	 */
	protected function getTable()
	{
		$table = $this->database->table("Roles");
		if (!$this->deleted) {
			$table->where("Deleted", false);
		}
		return $table;
	}

	/**
	 * Find 1 entity by ID
	 *
	 * @param string $id
	 *
	 * @return Role
	 */
	public function find($id)
	{
		$values["role"] = $id;
		$deleted = $this->deleted;
		$this->deleted = true;

		$item = $this->findOneBy($values);

		$this->deleted = $deleted;
		return $item;
	}

	/**
	 * Find first entity by $values (key=>value)
	 *
	 * @param array $values
	 * @param array $order Order => column=>ASC/DESC
	 *
	 * @return Role
	 */
	public function findOneBy(array $values, $order = null)
	{
		$items = $this->findBy($values, $order, 1);
		if (count($items) > 0) {
			return $items[0];
		} else {
			return null;
		}
	}

	/**
	 * Delete item
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
		$deletedRow = 0;
		if (($item = $this->find($id))) {

			$deleted = $this->deleted;
			$this->deleted = true;

			$deletedRow = $this->getTable()
				->where("Role", $id)
				->update(
					[
						"Deleted" => true,
					]
				);

			$this->deleted = $deleted;
		}
		return $deletedRow > 0;
	}

	/**
	 * Permanently delete item
	 *
	 * @param string $id
	 *
	 * @return boolean
	 */
	public function deletePermanently($id)
	{
		$deletedRow = 0;
		if (($item = $this->find($id))) {

			$deleted = $this->deleted;
			$this->deleted = true;

			$deletedRow = $this->getTable()
				->where("Role", $id)
				->delete();

			$this->deleted = $deleted;
		}
		return $deletedRow > 0;
	}

	/**
	 * from data to item
	 *
	 * @param \Nette\Database\IRow $data
	 *
	 * @return Role
	 */
	protected function dataToItem($data)
	{
		$item = new Role;

		foreach ($this->mapItemPropertySQLNames() as $property => $columnSql) {
			$item->$property = $data->$columnSql;
		}

		// not new item, set loadedRole
		$item->loadedRole = $item->role;

		return $item;
	}
}
