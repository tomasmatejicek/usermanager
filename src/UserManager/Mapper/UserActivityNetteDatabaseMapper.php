<?php

namespace Mepatek\UserManager\Mapper;

use Mepatek\Mapper\AbstractNetteDatabaseMapper,
	Mepatek\Mapper\IMapper;

use Nette,
	Nette\Database\Context,
	Mepatek\UserManager\Entity\UserActivity;

/**
 * Class UserActivityNetteDatabaseMapper
 * @package Mepatek\UserManager\Mapper
 */
class UserActivityNetteDatabaseMapper extends AbstractNetteDatabaseMapper implements IMapper
{
	/** @var Context */
	private $database;

	/**
	 * UserActivityNetteDatabaseMapper constructor.
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
	 * @param UserActivity $item
	 *
	 * @return boolean
	 */
	public function save(&$item)
	{
		$data = $this->itemToData($item);
		$retSave = false;

		if (!$item->id) { // new --> insert

			unset($data["UserActivityID"]);
			$data["ActivityDateTime"] = new Nette\Utils\DateTime();

			$row = $this->getTable()
				->insert($data);
			if ($row) {
				$item->id = $row["UserActivityID"];
				$item->datetime = $row["ActivityDateTime"];
				$retSave = true;
			}
		} else { // update
			unset($data["UserActivityID"]);
			unset($data["ActivityDateTime"]);

			$row = $this->getTable()
				->where("UserActivityID", $item->id)
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
	 * @param UserActivity $item
	 *
	 * @return array
	 */
	private function itemToData(UserActivity $item)
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
			"id"          => "UserActivityID",
			"userId"      => "UserID",
			"ip"          => "IP",
			"type"        => "ActivityType",
			"datetime"    => "ActivityDateTime",
			"description" => "Description",
		];
	}

	/**
	 * Get view object
	 * @return \Nette\Database\Table\Selection
	 */
	protected function getTable()
	{
		$table = $this->database->table("UsersActivity");
		return $table;
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
			$deletedRow = $this->getTable()
				->where("UserActivityID", $id)
				->delete();
		}
		return $deletedRow > 0;
	}

	/**
	 * Find 1 entity by ID
	 *
	 * @param string $id
	 *
	 * @return UserActivity
	 */
	public function find($id)
	{
		$values["id"] = $id;
		$item = $this->findOneBy($values);
		return $item;
	}

	/**
	 * Find first entity by $values (key=>value)
	 *
	 * @param array $values
	 * @param array $order Order => column=>ASC/DESC
	 *
	 * @return UserActivity
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
	 * from data to item
	 *
	 * @param \Nette\Database\IRow $data
	 *
	 * @return UserActivity
	 */
	protected function dataToItem($data)
	{
		$item = new UserActivity;

		foreach ($this->mapItemPropertySQLNames() as $property => $columnSql) {
			$item->$property = $data->$columnSql;
		}

		return $item;
	}
}
