<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../libs/AssertRunner.php';

Tester\Environment::setup();

/**
 * @return void
 */
function test(callable $cb)
{
	try {
		$cb();

	} catch (CzProject\GitPhp\GitException $e) {
		$result = $e->getRunnerResult();

		if ($result !== NULL) {
			echo $result->getCommand(), "\n";
			echo 'EXIT CODE: ', $result->getExitCode(), "\n";
			echo "--------------\n",
				$result->getOutputAsString(), "\n";

			if ($result->hasErrorOutput()) {
				echo "--------------\n",
					implode("\n", $result->getErrorOutput()), "\n";
			}
		}

		throw $e;
	}
}
