<?php

declare(strict_types=1);

use Tester\Assert;
use CzProject\GitPhp\CommandProcessor;
use CzProject\GitPhp\CommitId;
use CzProject\GitPhp\InvalidArgumentException;
use CzProject\GitPhp\InvalidStateException;

require __DIR__ . '/bootstrap.php';


test(function () {
	$options = [
		'first',
		[
			'--second' => TRUE,
			'--third' => '"go"',
		],
	];

	$env = [
		'ENV_1' => 'value1',
		'ENV_2' => 'value2',
	];

	$processor = new CommandProcessor(CommandProcessor::MODE_WINDOWS);
	Assert::same('set ENV_1=value1 && set ENV_2=value2 && git first --second 1 --third """go"""', $processor->process('git', $options, $env));

	$processor = new CommandProcessor(CommandProcessor::MODE_NON_WINDOWS);
	Assert::same('ENV_1=value1 ENV_2=value2 git first --second 1 --third \'"go"\'', $processor->process('git', $options, $env));
});


test(function () {
	$options = [
		'first',
		[
			'--second' => new CommitId('734713bc047d87bf7eac9674765ae793478c50d3'),
			'--one' => TRUE,
			'--two' => FALSE,
			'--three' => NULL,
			TRUE,
			FALSE,
			NULL,
		],
		NULL,
		'arg',
		new CommitId('734713bc047d87bf7eac9674765ae793478c50d3'),
	];

	$processor = new CommandProcessor(CommandProcessor::MODE_NON_WINDOWS);
	Assert::same(implode(' ', [
		'git',
		'first',
		'--second 734713bc047d87bf7eac9674765ae793478c50d3',
		'--one 1',
		'--two 0',
		'1',
		'0',
		'arg',
		'734713bc047d87bf7eac9674765ae793478c50d3',
	]), $processor->process('git', $options));
});


test(function () {
	$processor = new CommandProcessor(CommandProcessor::MODE_NON_WINDOWS);

	Assert::exception(function () use ($processor) {
		$processor->process('git', [
			(object) [],
		]);
	}, InvalidStateException::class, 'Unknow argument type stdClass.');

	Assert::exception(function () use ($processor) {
		$processor->process('git', [
			TRUE,
		]);
	}, InvalidStateException::class, 'Unknow argument type boolean.');

	Assert::exception(function () use ($processor) {
		$processor->process('git', [
			FALSE,
		]);
	}, InvalidStateException::class, 'Unknow argument type boolean.');

	Assert::exception(function () use ($processor) {
		$processor->process('git', [
			[
				(object) [],
			],
		]);
	}, InvalidStateException::class, 'Unknow option value type stdClass.');
});


test(function () {

	Assert::exception(function () {
		$processor = new CommandProcessor('INVALID');

	}, InvalidArgumentException::class, "Invalid mode 'INVALID'.");
});
