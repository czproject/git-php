<?php

use Tester\Assert;
use CzProject\GitPhp\Git;
use CzProject\GitPhp\GitException;
use CzProject\GitPhp\Tests\AssertRunner;

require __DIR__ . '/bootstrap.php';

$runner = new AssertRunner(__DIR__);
$git = new Git($runner);

$runner->assert(['clone', '-q', '--end-of-options', 'git@github.com:czproject/git-php.git', __DIR__]);
$runner->assert(['remote', 'add', '--end-of-options', 'origin2', 'git@github.com:czproject/git-php.git']);
$runner->assert(['remote', 'add', '--end-of-options', 'remote', 'git@github.com:czproject/git-php.git']);
$runner->assert(['remote', 'add', [
	'--mirror=push',
], '--end-of-options', 'only-push', 'test-url']);
$runner->assert(['remote', 'rename', '--end-of-options', 'remote', 'origin3']);
$runner->assert(['remote', 'set-url', [
	'--push',
], '--end-of-options', 'origin3', 'test-url']);
$runner->assert(['remote', 'remove', '--end-of-options', 'origin2']);

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

$runner->assert(['push', '--end-of-options', 'origin']);
$runner->assert(['fetch', '--end-of-options', 'origin']);
$runner->assert(['pull', '--end-of-options', 'origin']);
$repo->push('origin');
$repo->fetch('origin');
$repo->pull('origin');
