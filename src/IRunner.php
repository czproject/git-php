<?php

	declare(strict_types=1);

	namespace CzProject\GitPhp;


	interface IRunner
	{
		/**
		 * Add a configuration parameter to the command.
		 * @param string $name
		 * @param mixed $value
		 */
		function addConfig($name, $value);

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
