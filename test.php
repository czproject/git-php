<?php
require __DIR__ . '/src/IGit.php';
require __DIR__ . '/src/GitRepository.php';

use Cz\Git\GitRepository;

$repo = new GitRepository(".");
var_dump($repo->getChanges());