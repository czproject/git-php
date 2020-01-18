<?php

use Tester\Assert;
use CzProject\GitPhp\Git;
use CzProject\GitPhp\GitException;
use CzProject\GitPhp\Runners\MemoryRunner;

require __DIR__ . '/bootstrap.php';

$runner = new MemoryRunner(__DIR__);
$git = new Git($runner);
$repo = $git->open(__DIR__);

$runner->setResult(['branch', '-a'], [], [
	'  master',
	'* develop',
	'  remotes/origin/master',
]);
Assert::same([
	'master',
	'develop',
	'remotes/origin/master',
], $repo->getBranches());


$runner->setResult(['branch', '-a'], [], []);
Assert::null($repo->getBranches());
