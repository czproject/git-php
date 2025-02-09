<?php

use CzProject\GitPhp\GitConfig;
use Tester\Assert;
use CzProject\GitPhp\Git;
use CzProject\GitPhp\Tests\AssertRunner;

require __DIR__ . '/bootstrap.php';

$runner = new AssertRunner(__DIR__);
$git = new Git($runner);

$repo = $git->open(__DIR__);
$config = $repo->getGitConfig();
Assert::same(GitConfig::class, get_class($config));

$runner->assert(['config', 'user.name', 'tester']);
$config->set('user.name', 'tester');

$runner->assert(['config', '--global', 'user.name', 'tester']);
$config->set('user.name', 'tester', '--global');

$runner->assert(['config', 'user.name']);
$config->get('user.name');

$runner->assert(['config', '--local', 'user.name']);
$config->get('user.name', ['--local']);
