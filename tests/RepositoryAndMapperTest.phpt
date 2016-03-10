<?php
/**
 * TEST: Test all providers with all mappers
 */

namespace Test;

use Mepatek\UserManager\Entity\User;
use Mepatek\UserManager\Entity\UserActivity;
use Mepatek\UserManager\Entity\Role;
use Nette,
	Tester,
	Tester\Assert;

require __DIR__ . '/bootstrap.php';


class RepositoryAndMapperTest extends Tester\TestCase
{
	const TRANSACTION = true;

	private $database;

	private $varchar20;
	private $varchar30;
	private $varchar40;
	private $varchar50;
	private $varchar60;
	private $varchar100;
	private $varchar150;
	private $varchar200;
	private $varchar250;
	private $varchar255;
	private $binaryData;
	private $text;
	private $datetime;
	private $float;

	function __construct()
	{
	}


	function setUp()
	{
		$connection = new \Nette\Database\Connection(
			"sqlite:" . __DIR__ . "/data/UserManager.db",
			null, null
		);
		$structure = new \Nette\Database\Structure($connection, new \Nette\Caching\Storages\FileStorage(TEMP_DIR));
		$conventions = new \Nette\Database\Conventions\DiscoveredConventions($structure);
		$this->database = new \Nette\Database\Context($connection, $structure, $conventions);


		$this->varchar20 = (string)Nette\Utils\Random::generate(20);
		$this->varchar30 = (string)Nette\Utils\Random::generate(30);
		$this->varchar40 = (string)Nette\Utils\Random::generate(40);
		$this->varchar50 = (string)Nette\Utils\Random::generate(50);
		$this->varchar60 = (string)Nette\Utils\Random::generate(60);
		$this->varchar100 = (string)Nette\Utils\Random::generate(100);
		$this->varchar150 = (string)Nette\Utils\Random::generate(150);
		$this->varchar200 = (string)Nette\Utils\Random::generate(200);
		$this->varchar250 = (string)Nette\Utils\Random::generate(250);
		$this->varchar255 = (string)Nette\Utils\Random::generate(255);
		$this->text = (string)Nette\Utils\Random::generate(8000);
		$this->binaryData = (string)Nette\Utils\Random::generate(2048);
		$this->datetime = new \Nette\Utils\DateTime();
		$this->datetime1 = new \Nette\Utils\DateTime("2015-12-12");
		$this->date = new \Nette\Utils\DateTime("2015-12-10");
		$this->float = ((float)Nette\Utils\Random::generate(10, "0-9")) / 1000;
	}


	function testUserRepositoryNetteDatabaseMapper()
	{
		$mapper = new \Mepatek\UserManager\Mapper\UserNetteDatabaseMapper($this->database);
		$repository = new \Mepatek\UserManager\Repository\UserRepository($mapper);

		$this->beginTransaction();

		$user = new \Mepatek\UserManager\Entity\User;

		$user->fullName = $this->varchar255;
		$user->userName = $this->varchar50;
		$user->email = $this->varchar255;
		$user->phone = $this->varchar255;
		$user->created = $this->datetime;
		$user->lastLogged = $this->datetime;
		$user->disabled = true;
		$user->deleted = false;

		Assert::true($repository->save($user));

		$itemId = $user->id;
		Assert::type("integer", $itemId);

		$item1 = $repository->find($itemId);
		Assert::type("Mepatek\\UserManager\\Entity\\User", $item1);
		Assert::equal($user, $item1);

		// update
		$item1->fullName = "1";
		$item1->userName = "1";
		$item1->email = "1";
		$item1->phone = "1";
		$item1->lastLogged = $this->datetime1;
		$item1->disabled = false;
		$item1->addRole("admin");
		Assert::true($repository->save($item1));

		// find
		$item2 = $repository->find($itemId);
		Assert::equal($item1, $item2);
		Assert::notEqual($user, $item2);

		// delete and findBy
		Assert::equal([$item2], $repository->findBy(["id" => $itemId]));
		Assert::true($repository->delete($itemId));
		Assert::equal([], $repository->findBy(["id" => $itemId]));


		// delete permanently
		Assert::true($repository->deletePermanently($itemId));

		// find - NOT FOUND (NULL) is OK
		Assert::null($repository->find($itemId));

		$this->rollBack();
	}


	function testUserActivityRepositoryNetteDatabaseMapper()
	{
		$mapper = new \Mepatek\UserManager\Mapper\UserActivityNetteDatabaseMapper($this->database);
		$repository = new \Mepatek\UserManager\Repository\UserActivityRepository($mapper);

		$this->beginTransaction();

		$userActivity = new \Mepatek\UserManager\Entity\UserActivity();

		$userActivity->userId = 1;
		$userActivity->ip = $this->varchar50;
		$userActivity->type = $this->varchar30;
		$userActivity->datetime = $this->datetime;
		$userActivity->description = $this->text;

		Assert::true($repository->save($userActivity));

		$itemId = $userActivity->id;
		Assert::type("integer", $itemId);

		$item1 = $repository->find($itemId);
		Assert::type("Mepatek\\UserManager\\Entity\\UserActivity", $item1);
		Assert::equal($userActivity, $item1);

		// update
		$item1->ip = "1";
		$item1->type = "1";
		$item1->description = "1";
		Assert::true($repository->save($item1));

		// find
		$item2 = $repository->find($itemId);
		Assert::equal($item1, $item2);
		Assert::notEqual($userActivity, $item2);

		// delete and findBy
		Assert::equal([$item2], $repository->findBy(["id" => $itemId]));
		Assert::true($repository->delete($itemId));
		Assert::equal([], $repository->findBy(["id" => $itemId]));

		// find - NOT FOUND (NULL) is OK
		Assert::null($repository->find($itemId));

		$this->rollBack();
	}

	function testRoleRepositoryNetteDatabaseMapper()
	{
		$mapper = new \Mepatek\UserManager\Mapper\RoleNetteDatabaseMapper($this->database);
		$repository = new \Mepatek\UserManager\Repository\RoleRepository($mapper);

		$this->beginTransaction();

		$role = new \Mepatek\UserManager\Entity\Role();

		$role->role = $this->varchar30;
		$role->name = $this->varchar100;
		$role->description = $this->text;

		Assert::true($repository->save($role));

		$itemId = $role->role;
		Assert::type("string", $itemId);

		$item1 = $repository->find($itemId);
		Assert::type("Mepatek\\UserManager\\Entity\\Role", $item1);
		Assert::equal($role, $item1);

		// update
		$item1->role = "1";
		$item1->name = "1";
		$item1->description = "1";

		Assert::true($repository->save($item1));
		$itemId = "1";
		$item1->loadedRole = $item1->role;

		// find
		$item2 = $repository->find($itemId);
		Assert::equal($item1, $item2);

		// delete and findBy
		Assert::equal([$item2], $repository->findBy(["role" => $itemId]));
		Assert::true($repository->delete($itemId));
		Assert::equal([], $repository->findBy(["role" => $itemId]));

		// delete permanently
		Assert::true($repository->deletePermanently($itemId));

		// find - NOT FOUND (NULL) is OK
		Assert::null($repository->find($itemId));

		$this->rollBack();
	}


	/**
	 * If TRANSACTION is set to TRUE, begin database transaction
	 */
	private function beginTransaction()
	{
		if (self::TRANSACTION) {
			$this->database->beginTransaction();
		}
	}

	/**
	 * If TRANSACTION is set to TRUE, rollBack
	 */
	private function rollBack()
	{
		if (self::TRANSACTION) {
			$this->database->rollBack();
		}
	}
}


$test = new RepositoryAndMapperTest();
$test->run();
