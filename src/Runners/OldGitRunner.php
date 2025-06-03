<?php

	declare(strict_types=1);

	namespace CzProject\GitPhp\Runners;

	use CzProject\GitPhp\IRunner;


	class OldGitRunner implements IRunner
	{
		/** @var IRunner */
		private $runner;


		public function __construct(?IRunner $runner = NULL)
		{
			$this->runner = $runner !== NULL ? $runner : new CliRunner;
		}


		public function run($cwd, array $args, ?array $env = NULL)
		{
			if (($key = array_search('--end-of-options', $args)) !== FALSE) {
				unset($args[$key]);
			}

			return $this->runner->run($cwd, $args, $env);
		}


		public function getCwd()
		{
			return $this->runner->getCwd();
		}
	}
