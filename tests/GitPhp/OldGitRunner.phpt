<?php

declare(strict_types=1);

use CzProject\GitPhp\Git;
use CzProject\GitPhp\Runners\OldGitRunner;
use CzProject\GitPhp\Tests\AssertRunner;

require __DIR__ . '/bootstrap.php';

$assertRunner = new AssertRunner(__DIR__);
$runner = new OldGitRunner($assertRunner);
$git = new Git($runner);

$assertRunner->assert(['branch', 'master']);
$assertRunner->assert(['branch', 'develop']);
$assertRunner->assert(['checkout', 'develop']);
$assertRunner->assert(['merge', 'feature-1']);
$assertRunner->assert(['branch', '-d', 'feature-1']);
$assertRunner->assert(['checkout', 'master']);

$repo = $git->open(__DIR__);
$repo->createBranch('master');
$repo->createBranch('develop', TRUE);
$repo->merge('feature-1');
$repo->removeBranch('feature-1');
$repo->checkout('master');
