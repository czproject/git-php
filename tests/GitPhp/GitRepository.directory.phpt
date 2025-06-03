<?php

declare(strict_types=1);

use Tester\Assert;
use CzProject\GitPhp\Git;
use CzProject\GitPhp\GitException;
use CzProject\GitPhp\Runners\MemoryRunner;

require __DIR__ . '/bootstrap.php';

$runner = new MemoryRunner(__DIR__);
$git = new Git($runner);

$repoA = $git->open(__DIR__);
Assert::same(__DIR__, $repoA->getRepositoryPath());

$repoA = $git->open(__DIR__ . '/.git');
Assert::same(__DIR__, $repoA->getRepositoryPath());

$repoA = $git->open(__DIR__ . '/.git/');
Assert::same(__DIR__, $repoA->getRepositoryPath());


Assert::exception(function () use ($git) {
	$git->open(__DIR__ . '/unexists');

}, GitException::class, "Repository '" . __DIR__ . "/unexists' not found.");
