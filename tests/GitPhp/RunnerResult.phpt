<?php

declare(strict_types=1);

use Tester\Assert;
use CzProject\GitPhp\RunnerResult;

require __DIR__ . '/bootstrap.php';

test(function () {
	$result = new RunnerResult('cat-file', 0, "foo\r\nbar\r\n", "error\r\nerror2\r\n");

	Assert::true($result->hasOutput());
	Assert::same(['foo', 'bar'], $result->getOutput());
	Assert::same("foo\r\nbar\r\n", $result->getOutputAsString());
	Assert::same('bar', $result->getOutputLastLine());

	Assert::true($result->hasErrorOutput());
	Assert::same(['error', 'error2'], $result->getErrorOutput());
	Assert::same("error\r\nerror2\r\n", $result->getErrorOutputAsString());
});


test(function () {
	$result = new RunnerResult('cat-file', 0, "\r\n", "\r\n");

	Assert::false($result->hasOutput());
	Assert::same([], $result->getOutput());
	Assert::same("\r\n", $result->getOutputAsString());
	Assert::null($result->getOutputLastLine());

	Assert::false($result->hasErrorOutput());
	Assert::same([], $result->getErrorOutput());
	Assert::same("\r\n", $result->getErrorOutputAsString());
});


test(function () {
	$result = new RunnerResult('cat-file', 0, '', '');

	Assert::false($result->hasOutput());
	Assert::same([], $result->getOutput());
	Assert::same('', $result->getOutputAsString());
	Assert::null($result->getOutputLastLine());

	Assert::false($result->hasErrorOutput());
	Assert::same([], $result->getErrorOutput());
	Assert::same('', $result->getErrorOutputAsString());
});


test(function () {
	$result = new RunnerResult('cat-file', 0, ['foo', 'bar'], ['error', 'error2']);

	Assert::true($result->hasOutput());
	Assert::same(['foo', 'bar'], $result->getOutput());
	Assert::same("foo\nbar", $result->getOutputAsString());
	Assert::same('bar', $result->getOutputLastLine());

	Assert::true($result->hasErrorOutput());
	Assert::same(['error', 'error2'], $result->getErrorOutput());
	Assert::same("error\nerror2", $result->getErrorOutputAsString());
});


test(function () {
	$result = new RunnerResult('cat-file', 0, [], []);

	Assert::false($result->hasOutput());
	Assert::same([], $result->getOutput());
	Assert::same('', $result->getOutputAsString());
	Assert::null($result->getOutputLastLine());

	Assert::false($result->hasErrorOutput());
	Assert::same([], $result->getErrorOutput());
	Assert::same('', $result->getErrorOutputAsString());
});
