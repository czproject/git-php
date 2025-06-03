<?php

	declare(strict_types=1);

	namespace CzProject\GitPhp;


	interface IRunner
	{
		/**
		 * @param  string $cwd
		 * @param  array<mixed> $args
		 * @param  array<string, scalar>|NULL $env
		 * @return RunnerResult
		 */
		function run($cwd, array $args, ?array $env = NULL);


		/**
		 * @return string
		 */
		function getCwd();
	}
