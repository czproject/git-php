<?php

declare(strict_types=1);

use Tester\Assert;
use CzProject\GitPhp\Git;
use CzProject\GitPhp\Runners\MemoryRunner;

require __DIR__ . '/bootstrap.php';

$runner = new MemoryRunner(__DIR__);
$git = new Git($runner);
$repo = $git->open(__DIR__);

$runner->setResult(['branch'], [], [
	'* master',
]);
Assert::same([
	'* master',
], $repo->execute('branch'));


$runner->setResult(['remote', '-v'], [], []);
Assert::same([], $repo->execute(['remote', '-v']));

$runner->setResult(['remote', 'add', 'origin', 'https://github.com/czproject/git-php.git'], [], []);
$repo->execute(['remote', 'add', 'origin', 'https://github.com/czproject/git-php.git']);

$runner->setResult(['remote', '-v'], [], [
	"origin\thttps://github.com/czproject/git-php.git (fetch)",
	"origin\thttps://github.com/czproject/git-php.git (push)",
]);
Assert::same([
	"origin\thttps://github.com/czproject/git-php.git (fetch)",
	"origin\thttps://github.com/czproject/git-php.git (push)",
], $repo->execute(['remote', '-v']));


$runner->setResult(['blabla'], [], [], [], 1);
Assert::exception(function () use ($repo) {
	$repo->execute('blabla');
}, CzProject\GitPhp\GitException::class, "Command 'git blabla' failed (exit-code 1).");
