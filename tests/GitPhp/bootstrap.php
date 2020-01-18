<?php

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../libs/AssertRunner.php';

Tester\Environment::setup();


function test($cb)
{
	$cb();
}
