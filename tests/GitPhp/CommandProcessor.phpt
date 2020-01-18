<?php

use Tester\Assert;
use CzProject\GitPhp\CommandProcessor;

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
