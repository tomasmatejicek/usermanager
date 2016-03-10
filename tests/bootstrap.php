<?php

if (!$loader = require __DIR__ . '/../vendor/autoload.php') {

	echo "Install composer\n";
	exit(1);
}

if (!class_exists('Tester\Assert')) {
	echo "Install Nette Tester using `composer update --dev`\n";
	exit(1);
}

$loader->addPsr4('Mepatek\\', __DIR__. '/src');

if (extension_loaded('xdebug')) {
	xdebug_disable();
}

Tester\Environment::setup();

date_default_timezone_set("Europe/Prague");

@mkdir(__DIR__ . '/temp');
define('TEMP_DIR', __DIR__ . '/temp/' . getmypid());
Tester\Helpers::purge(TEMP_DIR);


