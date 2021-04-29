<?php

	namespace CzProject\GitPhp;


	interface IRunner
	{
		/**
		 * @return RunnerResult
		 */
		function run($cwd, array $args, array $env = NULL);


		/**
		 * @return string
		 */
		function getCwd();
	}
