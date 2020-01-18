<?php
require __DIR__ . '/../../vendor/nette/tester/Tester/bootstrap.php';
require __DIR__ . '/../../src/exceptions.php';
require __DIR__ . '/../../src/Helpers.php';
require __DIR__ . '/../../src/CommandProcessor.php';
require __DIR__ . '/../../src/RunnerResult.php';
require __DIR__ . '/../../src/IRunner.php';
require __DIR__ . '/../../src/Runners/CliRunner.php';
require __DIR__ . '/../../src/GitRepository.php';
require __DIR__ . '/../../src/Git.php';

// create temporary directory
define('TEMP_DIR', __DIR__ . '/../tmp/' . (isset($_SERVER['argv']) ? md5(serialize($_SERVER['argv'])) : getmypid()));
@mkdir(TEMP_DIR, 0777, TRUE);
Tester\Helpers::purge(TEMP_DIR);


if (extension_loaded('xdebug'))
{
	Tester\CodeCoverage\Collector::start(__DIR__ . '/../coverage.dat');
}


function test($cb)
{
	$cb();
}
