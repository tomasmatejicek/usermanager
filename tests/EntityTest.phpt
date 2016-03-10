<?php

namespace Test;

use Mepatek\UserManager\Entity\User;
use Mepatek\UserManager\Entity\UserActivity;
use Mepatek\UserManager\Entity\Role;
use Nette,
	Tester,
	Tester\Assert;

require __DIR__ . '/bootstrap.php';

class EntityTest extends Tester\TestCase
{
	private $varchar20;
	private $varchar30;
	private $varchar40;
	private $varchar50;
	private $varchar60;
	private $varchar100;
	private $varchar150;
	private $varchar255;
	private $text;
	private $datetime;
	private $float;


	function __construct()
	{
	}


	function setUp()
	{
		$this->varchar20 = (string)Nette\Utils\Random::generate(20);
		$this->varchar30 = (string)Nette\Utils\Random::generate(30);
		$this->varchar40 = (string)Nette\Utils\Random::generate(40);
		$this->varchar50 = (string)Nette\Utils\Random::generate(50);
		$this->varchar60 = (string)Nette\Utils\Random::generate(60);
		$this->varchar100 = (string)Nette\Utils\Random::generate(100);
		$this->varchar150 = (string)Nette\Utils\Random::generate(150);
		$this->varchar255 = (string)Nette\Utils\Random::generate(255);
		$this->text = (string)Nette\Utils\Random::generate(8000);
		$this->datetime = new \Nette\Utils\DateTime();
		$this->float = ((float)Nette\Utils\Random::generate(10, "0-9")) / 1024;
	}


	function testUser()
	{

		$user = new User();

		$roles = [
			"Role 1",
			"Role 2",
		];

		$user->id = 10;
		$user->fullName = $this->varchar255;
		$user->userName = $this->varchar50;
		$user->email = $this->varchar255;
		$user->phone= $this->varchar255;
		$user->created = $this->datetime;
		$user->lastLogged = $this->datetime;
		$user->disabled = TRUE;
		$user->deleted = FALSE;
		$user->addRole("Role 1");
		$user->addRole("Role 2");

		// tests ...
		Assert::same(10, $user->id);
		Assert::same($this->varchar255, $user->fullName);
		Assert::same($this->varchar50, $user->userName);
		Assert::same($this->varchar255, $user->email);
		Assert::same($this->varchar255, $user->phone);
		Assert::equal($this->datetime, $user->created);
		Assert::equal($this->datetime, $user->lastLogged);
		Assert::equal($roles, $user->roles);
		Assert::true($user->disabled);
		Assert::false($user->deleted);
	}

	function testRole()
	{

		$role = new Role();

		$role->role = $this->varchar30;
		$role->name = $this->varchar100;
		$role->description = $this->text;

		// tests ...
		Assert::same($this->varchar30, $role->role);
		Assert::same($this->varchar100, $role->name);
		Assert::same($this->text, $role->description);
		Assert::false($role->loadedRole);
		$role->loadedRole = $this->varchar30;
		Assert::type("string", $role->loadedRole);
	}

	function testUserActivity()
	{

		$userActivity = new UserActivity();

		$userActivity->id = 8;
		$userActivity->userId = 9;
		$userActivity->ip = $this->varchar50;
		$userActivity->type = $this->varchar30;
		$userActivity->datetime = $this->datetime;;
		$userActivity->description = $this->text;

		// tests ...
		Assert::same(8, $userActivity->id);
		Assert::same(9, $userActivity->userId);
		Assert::same($this->varchar50, $userActivity->ip);
		Assert::same($this->varchar30, $userActivity->type);
		Assert::equal($this->datetime, $userActivity->datetime);
		Assert::same($this->text, $userActivity->description);
	}
}


$test = new EntityTest();
$test->run();
