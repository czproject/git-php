<?php
use Tester\Assert;
use CzProject\GitPhp\Git;
require __DIR__ . '/bootstrap.php';

$git = new Git;
Assert::true($git->isRemoteUrlReadable('https://github.com/czproject/git-php'));
Assert::false($git->isRemoteUrlReadable('https://github.com/czproject/git-php-404'));
