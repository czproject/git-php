<?php
use Tester\Assert;
use CzProject\GitPhp\GitRepository;
require __DIR__ . '/bootstrap.php';

$repo = GitRepository::init(TEMP_DIR);
file_put_contents($repo->getRepositoryPath() . '/readme.md', "README\n");
$repo->addFile($repo->getRepositoryPath() . '/readme.md');
$repo->commit('init commit');

Assert::same([
	'* master',
], $repo->execute('branch'));

Assert::same([], $repo->execute(['remote', '-v']));
$repo->execute(['remote', 'add', 'origin', 'https://github.com/czproject/git-php.git']);
Assert::same([
	"origin\thttps://github.com/czproject/git-php.git (fetch)",
	"origin\thttps://github.com/czproject/git-php.git (push)",
], $repo->execute(['remote', '-v']));


Assert::exception(function () use ($repo) {
	$repo->execute('blabla');
}, CzProject\GitPhp\GitException::class, "Command 'git 'blabla'' failed (exit-code 1).");
