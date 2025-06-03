<?php

declare(strict_types=1);

use Tester\Assert;
use CzProject\GitPhp\Git;
use CzProject\GitPhp\GitException;
use CzProject\GitPhp\Tests\AssertRunner;

require __DIR__ . '/bootstrap.php';

$runner = new AssertRunner(__DIR__);
$git = new Git($runner);
$repo = $git->open(__DIR__ . '/fixtures');


test(function () use ($repo, $runner) {
	$runner->resetAsserts();
	$runner->assert(['add', '--end-of-options', 'file1.txt']);
	$runner->assert(['add', '--end-of-options', 'file2.txt']);
	$runner->assert(['add', '--end-of-options', 'file3.txt']);
	$runner->assert(['add', '--end-of-options', 'file4.txt']);
	$runner->assert(['add', '--end-of-options', 'file5.txt']);

	$repo->addFile('file1.txt');
	$repo->addFile([
		'file2.txt',
		'file3.txt',
	]);
	$repo->addFile('file4.txt', 'file5.txt');
});


test(function () use ($repo) {
	Assert::exception(function () use ($repo) {
		$repo->addFile('not-found.txt');
	}, GitException::class, "The path at 'not-found.txt' does not represent a valid file.");
});


test(function () use ($repo, $runner) {
	$runner->resetAsserts();
	$runner->assert(['rm', '-r', '--end-of-options', 'file1.txt']);
	$runner->assert(['rm', '-r', '--end-of-options', 'file2.txt']);
	$runner->assert(['rm', '-r', '--end-of-options', 'file3.txt']);
	$runner->assert(['rm', '-r', '--end-of-options', 'file4.txt']);
	$runner->assert(['rm', '-r', '--end-of-options', 'file5.txt']);

	$repo->removeFile('file1.txt');
	$repo->removeFile([
		'file2.txt',
		'file3.txt',
	]);
	$repo->removeFile('file4.txt', 'file5.txt');
});


test(function () use ($repo, $runner) {
	$runner->resetAsserts();
	$runner->assert(['mv', '--end-of-options', 'file1.txt', 'new1.txt']);
	$runner->assert(['mv', '--end-of-options', 'file2.txt', 'new2.txt']);
	$runner->assert(['mv', '--end-of-options', 'file3.txt', 'new3.txt']);

	$repo->renameFile('file1.txt', 'new1.txt');
	$repo->renameFile([
		'file2.txt' => 'new2.txt',
		'file3.txt' => 'new3.txt',
	]);
});


test(function () use ($repo, $runner) {
	$runner->resetAsserts();
	$runner->assert(['add', '--all']);

	$repo->addAllChanges();
});



test(function () use ($repo, $runner) {
	$runner->resetAsserts();
	$runner->assert(['update-index', '-q', '--refresh']);
	$runner->assert(['status', '--porcelain']);

	Assert::false($repo->hasChanges());
});
