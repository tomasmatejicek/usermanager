<?php

namespace Mepatek\UserManager\Mapper;

use Nette,
	Nette\Database\Context,
	App\Model\Logger,
	Mepatek\UserManager\Entity\User;

/**
 * Class UserNetteDatabaseMapper
 * @package Mepatek\UserManager\Mapper
 */
class UserNetteDatabaseMapper extends AbstractNetteDatabaseMapper implements IMapper
{
	/** @var Nette\Database\Context */
	private $database;

	/** @var boolean TRUE - find deleted row */
	private $deleted;

	/**
	 * UserNetteDatabaseMapper constructor.
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

			unset($data["UserID"]);
			$data["Created"] = new Nette\Utils\DateTime();

			$row = $this->getTable()
				->insert($data);
			if ($row) {
				$item->id = $row["UserID"];
				$this->logInsert(__CLASS__, $item);
				$retSave = true;
			}
		} else { // update
			$item_old = $this->find($item->id);
			unset($data["UserID"]);
			unset($data["Created"]);

			$row = $this->getTable()
				->where("UserID", $item->id)
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
				->where("UserID", $id)
				->update(
					array(
						"Deleted" => TRUE,
					)
				);

			$this->deleted = $deleted;

			$this->logDelete(__CLASS__, $item, "UPDATE Users SET Deleted WHERE UserID=" . $id . " (cnt: $deletedRow)");
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
				->where("UserID", $id)
				->delete();

			$this->deleted = $deleted;

			$this->logDelete(__CLASS__, $item, "DELETE FROM Users WHERE UserID=" . $id . " (cnt: $deletedRow)");
		}
		return $deletedRow > 0;
	}

	/**
	 * Find 1 entity by ID
	 *
	 * @param string $id
	 * @return User
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
	* @return User
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
		$table = $this->database->table("Users");
		if ( ! $this->deleted ) {
			$table->where("Deleted",FALSE);
		}
		return $table;
	}

	/**
	 * Item data to array
	 *
	 * @param User $item
	 * @return array
	 */
	private function itemToData(User $item)
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
	 * @return User
	 */
	protected function dataToItem($data)
	{
		$item = new User;

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
			"id"			=> "UserID",
			"fullName"		=> "FullName",
			"userName"		=> "UserName",
			"email"			=> "Email",
			"phone"			=> "Phone",
			"created"		=> "Created",
			"lastLogged"	=> "LastLogged",
			"disabled"		=> "Disabled",
			"deleted"		=> "Deleted",
		);
	}
}
