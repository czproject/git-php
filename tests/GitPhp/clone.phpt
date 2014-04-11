<?php
use Tester\Assert;
use Cz\Git\GitRepository;
require __DIR__ . '/bootstrap.php';
require __DIR__ . '/../../src/IGit.php';
require __DIR__ . '/../../src/GitRepository.php';

$cwd = getcwd();
chdir(TEMP_DIR);
$repo = GitRepository::cloneRepository('https://github.com/czproject/git-php.git');
chdir($cwd);

// repo already exists
#Assert::exception(function() {
#	GitRepository::init(TEMP_DIR);
#}, 'Cz\Git\GitException');

Assert::same(realpath(TEMP_DIR . '/git-php'), $repo->getRepositoryPath());

// repo is empty
Assert::false($repo->isChanges());
$tags = $repo->getTags();
Assert::true(in_array('v1.0.0', $tags));
Assert::true(in_array('v1.0.1', $tags));
Assert::true(in_array('v2.0.0', $tags));

$branches = $repo->getBranches();
Assert::true(in_array('master', $branches));
Assert::true(in_array('remotes/origin/master', $branches));
Assert::true(in_array('remotes/origin/version-2', $branches));

Assert::same(array('master'), $repo->getLocalBranches());

// Specificky adresar
Tester\Helpers::purge(TEMP_DIR);
$repo = GitRepository::cloneRepository('https://github.com/czproject/git-php.git', TEMP_DIR . '/git-php2');

// repo already exists
#Assert::exception(function() {
#	GitRepository::init(TEMP_DIR);
#}, 'Cz\Git\GitException');

Assert::same(realpath(TEMP_DIR . '/git-php2'), $repo->getRepositoryPath());

// repo is empty
Assert::false($repo->isChanges());
$tags = $repo->getTags();
Assert::true(in_array('v1.0.0', $tags));
Assert::true(in_array('v1.0.1', $tags));
Assert::true(in_array('v2.0.0', $tags));

$branches = $repo->getBranches();
Assert::true(in_array('master', $branches));
Assert::true(in_array('remotes/origin/master', $branches));
Assert::true(in_array('remotes/origin/version-2', $branches));

Assert::same(array('master'),$repo->getLocalBranches());

