<?php

	namespace CzProject\GitPhp\Runners;

	use CzProject\GitPhp\IRunner;


	class OldGitRunner implements IRunner
	{
		/** @var IRunner */
		private $runner;


		public function __construct(IRunner $runner = NULL)
		{
			$this->runner = $runner !== NULL ? $runner : new CliRunner;
		}

		/**
		 * Add a configuration parameter to the command.
		 * @param string $name
		 * @param mixed $value
		 */
		public function addConfig($name, $value)
		{
			$this->runner->addConfig($name, $value);
		}

		public function run($cwd, array $args, array $env = NULL)
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
