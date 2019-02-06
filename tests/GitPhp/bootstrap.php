<?php
require __DIR__ . '/../../vendor/nette/tester/Tester/bootstrap.php';
require __DIR__ . '/../../src/exceptions.php';
require __DIR__ . '/../../src/Helpers.php';
require __DIR__ . '/../../src/GitRepository.php';

// create temporary directory
define('TEMP_DIR', __DIR__ . '/../tmp/' . (isset($_SERVER['argv']) ? md5(serialize($_SERVER['argv'])) : getmypid()));
@mkdir(TEMP_DIR, 0777, TRUE);
Tester\Helpers::purge(TEMP_DIR);


if (extension_loaded('xdebug'))
{
	Tester\CodeCoverage\Collector::start(__DIR__ . '/../coverage.dat');
}
