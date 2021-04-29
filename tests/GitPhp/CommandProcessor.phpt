<?php

use Tester\Assert;
use CzProject\GitPhp\CommandProcessor;
use CzProject\GitPhp\CommitId;
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
});
