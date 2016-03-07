<?php

namespace Mepatek\UserManager\Mapper;

use Nette,
	Nette\Database\Context,
	App\Model\Logger,
	Mepatek\UserManager\Entity\Role;

/**
 * Class RoleNetteDatabaseMapper
 * @package Mepatek\UserManager\Mapper
 */
class RoleNetteDatabaseMapper extends AbstractNetteDatabaseMapper implements IMapper
{
	/** @var Nette\Database\Context */
	private $database;

	/** @var boolean TRUE - find deleted row */
	private $deleted;

	/**
	 * RoleNetteDatabaseMapper constructor.
	 * @param Context $database
	 * @param Logger|null $logger
	 */
	public function __construct(Context $database, Logger $logger=null)
	{
		$this->database = $database;
		$this->logger = $logger;
	}

	/**
	 * Save item
	 * @param Task $item
	 * @return boolean
	 */
	public function save(&$item)
	{
		$data = $this->itemToData($item);
		$retSave = false;

		if (! $item->id) { // new --> insert

			unset($data["RoleID"]);
			$data["Created"] = new Nette\Utils\DateTime();

			$row = $this->getTable()
				->insert($data);
			if ($row) {
				$item->id = $row["RoleID"];
				$this->logInsert(__CLASS__, $item);
				$retSave = true;
			}
		} else { // update
			$item_old = $this->find($item->id);
			unset($data["RoleID"]);
			unset($data["Created"]);

			$row = $this->getTable()
				->where("RoleID", $item->id)
				->update($data);
			if ($row) {
				$this->logSave(__CLASS__, $item_old, $item);
				$retSave = true;
			}
		}

		return $retSave;
	}

	/**
	 * Delete item
	 * @param integer $id
	 * @return boolean
	 */
	public function delete($id)
	{
		$deletedRow = 0;
		if (($item = $this->find($id))) {

			$deleted = $this->deleted;
			$this->deleted = true;

			$deletedRow = $this->getTable()
				->where("RoleID", $id)
				->update(
					array(
						"Deleted" => TRUE,
					)
				);

			$this->deleted = $deleted;

			$this->logDelete(__CLASS__, $item, "UPDATE Roles SET Deleted WHERE RoleID=" . $id . " (cnt: $deletedRow)");
		}
		return $deletedRow > 0;
	}

	/**
	 * Permanently delete item
	 * @param integer $id
	 * @return boolean
	 */
	public function deletePermanently($id)
	{
		$deletedRow = 0;
		if (($item = $this->find($id))) {

			$deleted = $this->deleted;
			$this->deleted = true;

			$deletedRow = $this->getTable()
				->where("RoleID", $id)
				->delete();

			$this->deleted = $deleted;

			$this->logDelete(__CLASS__, $item, "DELETE FROM Roles WHERE RoleID=" . $id . " (cnt: $deletedRow)");
		}
		return $deletedRow > 0;
	}

	/**
	 * Find 1 entity by ID
	 *
	 * @param string $id
	 * @return Role
	 */
	public function find($id)
	{
		$values["id"] = $id;
		$deleted = $this->deleted;
		$this->deleted = true;

		$item = $this->findOneBy($values);

		$this->deleted = $deleted;
		return $item;
	}

	/**
	* Find first entity by $values (key=>value)
	* @param array $values
	* @param array $order Order => column=>ASC/DESC
	* @return Role
	*/
	public function findOneBy(array $values, $order=null)
	{
		$items = $this->findBy($values, $order, 1);
		if (count($items)>0) {
			return $items[0];
		} else {
			return NULL;
		}
	}


	/**
	* Get view object
	* @return \Nette\Database\Table\Selection
	*/
	protected function getTable()
	{
		$table = $this->database->table("Roles");
		if ( ! $this->deleted ) {
			$table->where("Deleted",FALSE);
		}
		return $table;
	}

	/**
	 * Item data to array
	 *
	 * @param Role $item
	 * @return array
	 */
	private function itemToData(Role $item)
	{
		$data = array();

		foreach ($this->mapItemPropertySQLNames() as $property => $columnSql) {
			$data[$columnSql] = $item->$property;
		}

		return $data;
	}

	/**
	 * from data to item
	 *
	 * @param \Nette\Database\IRow $data
	 * @return Role
	 */
	protected function dataToItem($data)
	{
		$item = new Role;

		foreach ($this->mapItemPropertySQLNames() as $property => $columnSql) {
			$item->$property = $data->$columnSql;
		}

		return $item;
	}


	/**
	 * Get array map of item property vs SQL columns name for Tasks table
	 * @return array
	 */
	protected function mapItemPropertySQLNames()
	{
		return array (
			"id"			=> "RoleID",
			"fullName"		=> "FullName",
			"RoleName"		=> "RoleName",
			"email"			=> "Email",
			"phone"			=> "Phone",
			"created"		=> "Created",
			"lastLogged"	=> "LastLogged",
			"disabled"		=> "Disabled",
			"deleted"		=> "Deleted",
		);
	}
}
