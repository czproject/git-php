<?php
use Tester\Assert;
use CzProject\GitPhp\GitRepository;
require __DIR__ . '/bootstrap.php';

$repo = GitRepository::init(TEMP_DIR);
file_put_contents($repo->getRepositoryPath() . '/readme.md', "README\n");
$repo->addFile($repo->getRepositoryPath() . '/readme.md');
$repo->commit('init commit');

Assert::same(array(
	'* master',
), $repo->execute('branch'));

Assert::same(array(), $repo->execute(array('remote', '-v')));
$repo->execute(array('remote', 'add', 'origin', 'https://github.com/czproject/git-php.git'));
Assert::same(array(
	"origin\thttps://github.com/czproject/git-php.git (fetch)",
	"origin\thttps://github.com/czproject/git-php.git (push)",
), $repo->execute(array('remote', '-v')));


Assert::exception(function () use ($repo) {
	$repo->execute('blabla');
}, 'CzProject\GitPhp\GitException', "Command 'git 'blabla'' failed (exit-code 1).");
