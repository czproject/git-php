<?php
use Tester\Assert;
use CzProject\GitPhp\GitRepository;
require __DIR__ . '/bootstrap.php';

Assert::true(GitRepository::isRemoteUrlReadable('https://github.com/czproject/git-php'));
Assert::false(GitRepository::isRemoteUrlReadable('https://github.com/czproject/git-php-404'));
