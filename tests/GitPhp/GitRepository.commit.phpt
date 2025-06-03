<?php

declare(strict_types=1);

use Tester\Assert;
use CzProject\GitPhp\Git;
use CzProject\GitPhp\GitException;
use CzProject\GitPhp\Tests\AssertRunner;

require __DIR__ . '/bootstrap.php';

$runner = new AssertRunner(__DIR__);
$git = new Git($runner);

$runner->assert(['commit', '-m', 'Commit message']);

$repo = $git->open(__DIR__);
$repo->commit('Commit message');
