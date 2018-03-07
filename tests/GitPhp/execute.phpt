<?php
use Tester\Assert;
use Cz\Git\GitRepository;
require __DIR__ . '/bootstrap.php';
require __DIR__ . '/../../src/IGit.php';
require __DIR__ . '/../../src/GitRepository.php';

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
}, 'Cz\Git\GitException', "Command 'git 'blabla'' failed (exit-code 1).");
