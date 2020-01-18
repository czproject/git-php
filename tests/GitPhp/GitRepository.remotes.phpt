<?php

use Tester\Assert;
use CzProject\GitPhp\Git;
use CzProject\GitPhp\GitException;
use CzProject\GitPhp\Tests\AssertRunner;

require __DIR__ . '/bootstrap.php';

$runner = new AssertRunner(__DIR__);
$git = new Git($runner);

$runner->assert(['clone', '-q', 'git@github.com:czproject/git-php.git', __DIR__]);
$runner->assert(['remote', 'add', 'origin2', 'git@github.com:czproject/git-php.git']);
$runner->assert(['remote', 'add', 'remote', 'git@github.com:czproject/git-php.git']);
$runner->assert(['remote', 'add', [
	'--mirror=push',
], 'only-push', 'test-url']);
$runner->assert(['remote', 'rename', 'remote', 'origin3']);
$runner->assert(['remote', 'set-url', [
	'--push',
], 'origin3', 'test-url']);
$runner->assert(['remote', 'remove', 'origin2']);

$repo = $git->cloneRepository('git@github.com:czproject/git-php.git', __DIR__);
$repo->addRemote('origin2', 'git@github.com:czproject/git-php.git');
$repo->addRemote('remote', 'git@github.com:czproject/git-php.git');
$repo->addRemote('only-push', 'test-url', [
	'--mirror=push',
]);
$repo->renameRemote('remote', 'origin3');
$repo->setRemoteUrl('origin3', 'test-url', [
	'--push',
]);
$repo->removeRemote('origin2');

$runner->assert(['push', 'origin']);
$runner->assert(['fetch', 'origin']);
$runner->assert(['pull', 'origin']);
$repo->push('origin');
$repo->fetch('origin');
$repo->pull('origin');
