<?php

namespace Mepatek\UserManager\Mapper;

use Mepatek\Mapper\AbstractNetteDatabaseMapper,
	Mepatek\Mapper\IMapper;

use Nette,
	Nette\Database\Context,
	Nette\Utils\DateTime,
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
	 *
	 * @param Context     $database
	 */
	public function __construct(Context $database)
	{
		$this->database = $database;
	}

	/**
	 * Save item
	 *
	 * @param Task $item
	 *
	 * @return boolean
	 */
	public function save(&$item)
	{
		$data = $this->itemToData($item);
		$retSave = false;

		if (!$item->id) { // new --> insert

			unset($data["UserID"]);
			$data["Created"] = new Nette\Utils\DateTime();

			$row = $this->getTable()
				->insert($data);
			if ($row) {
				$item->id = $row["UserID"];
				$item->created = $data["Created"];
				$this->saveRoles($item);
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
				$this->saveRoles($item);
				$retSave = true;
			}
		}

		return $retSave;
	}

	/**
	 * Item data to array
	 *
	 * @param User $item
	 *
	 * @return array
	 */
	private function itemToData(User $item)
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
			"id"         => "UserID",
			"fullName"   => "FullName",
			"userName"   => "UserName",
			"email"      => "Email",
			"phone"      => "Phone",
			"created"    => "Created",
			"lastLogged" => "LastLogged",
			"disabled"   => "Disabled",
			"deleted"    => "Deleted",
		];
	}

	/**
	 * Get view object
	 * @return \Nette\Database\Table\Selection
	 */
	protected function getTable()
	{
		$table = $this->database->table("Users");
		if (!$this->deleted) {
			$table->where("Deleted", false);
		}
		return $table;
	}

	/**
	 * Save roles from item
	 *
	 * @param User $item
	 */
	public function saveRoles($item)
	{
		// any changes?
		$oldroles = $this->getRoles($item->id);
		if ($oldroles === $item->roles) {
			// no? bye :)
			return;
		}

		// DELETE all roles
		$this->database
			->table("UsersRoles")
			->where("UserID", $item->id)
			->delete();

		foreach ($item->roles as $role) {
			// insert rules
			$this->database
				->table("UsersRoles")
				->insert(
					[
						"UserID" => $item->id,
						"Role"   => $role,
					]
				);
		}

	}

	/**
	 * Ger array of roles for user id
	 *
	 * @param integer $userId
	 *
	 * @return array
	 */
	private function getRoles($userId)
	{
		$roles = $this->database// get roles from table
		->table("UsersRoles")
			->select("Role")
			->where("UserID", $userId)
			->fetchPairs("Role", "Role");
		return array_values($roles);
	}

	/**
	 * Find 1 entity by ID
	 *
	 * @param string $id
	 *
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
	 *
	 * @param array $values
	 * @param array $order Order => column=>ASC/DESC
	 *
	 * @return User
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
				->where("UserID", $id)
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
	 * @param integer $id
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
				->where("UserID", $id)
				->delete();

			$this->deleted = $deleted;
		}
		return $deletedRow > 0;
	}

	/**
	 * Get password hash for user
	 * Null if not find
	 *
	 * @param User $item
	 *
	 * @return string|null
	 */
	public function getPassword(User $item)
	{
		$row = $this->getTable()
			->select("PwHash")
			->where("UserID", $item->id)
			->fetch();
		if ($row) {
			return $row->PwHash;
		} else {
			return null;
		}
	}

	/**
	 * Change password for user with $id
	 * Reset PwToken*
	 * False if not find or not change
	 *
	 * @param integer $id
	 * @param string  $newHashPassword
	 *
	 * @return boolean
	 */
	public function changePassword($id, $newHashPassword)
	{

		$rowCnt = $this->getTable()
			->where("UserID", $id)
			->update(
				[
					"PwHash"        => $newHashPassword,
					"PwToken"       => null,
					"PwTokenExpire" => null,
				]
			);

		return $rowCnt > 0;
	}

	/**
	 * Find user by token
	 *
	 * @param $token
	 *
	 * @return User|null
	 */
	public function findUserByToken($token)
	{
		$row = $this->getTable()
			->where("PwToken", $token)
			->where("PwTokenExpire >=", new DateTime())
			->fetch();
		if ($row) {
			return $this->dataToItem($row);
		} else {
			return null;
		}
	}

	/**
	 * from data to item
	 *
	 * @param \Nette\Database\IRow $data
	 *
	 * @return User
	 */
	protected function dataToItem($data)
	{
		$item = new User;

		foreach ($this->mapItemPropertySQLNames() as $property => $columnSql) {
			$item->$property = $data->$columnSql;
		}

		$this->loadRoles($item);

		return $item;
	}

	/**
	 * load roles to item
	 *
	 * @param User $item
	 */
	private function loadRoles(&$item)
	{
		// clear roles
		$item->deleteAllRoles();

		// set roles to item
		foreach ($this->getRoles($item->id) as $role) {
			$item->addRole($role);
		}
	}

	/**
	 * Reset and return password token
	 *
	 * @param User     $item
	 * @param DateTime $tokenExpire
	 *
	 * @return string new password token
	 */
	public function resetPasswordToken(User $item, DateTime $tokenExpire)
	{
		$token = md5(md5(uniqid(rand(), true)));
		$row = $this->getTable()
			->where("UserID", $item->id)
			->update(
				[
					"PwToken"       => $token,
					"PwTokenExpire" => $tokenExpire,
				]
			);
		if ($row > 0) {
			return $token;
		} else {
			return null;
		}
	}
}
