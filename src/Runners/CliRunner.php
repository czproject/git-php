<?php

	namespace CzProject\GitPhp\Runners;

	use CzProject\GitPhp\GitException;
	use CzProject\GitPhp\IRunner;
	use CzProject\GitPhp\RunnerResult;


	class CliRunner implements IRunner
	{
		/** @var string */
		private $gitBinary;


		/**
		 * @param  string
		 */
		public function __construct($gitBinary = 'git')
		{
			$this->gitBinary = $gitBinary;
		}


		/**
		 * @return RunnerResult
		 */
		public function run($cwd, array $args, array $env = NULL)
		{
			if (!is_dir($cwd)) {
				throw new GitException("Directory '$cwd' not found");
			}

			$oldCwd = getcwd();
			chdir($cwd);

			$cmd = $this->processCommand($args, $env);
			exec($cmd . ' 2>&1', $output, $returnCode);

			chdir($oldCwd);
			return new RunnerResult($cmd, $returnCode, $output);
		}


		/**
		 * @return string
		 */
		public function getCwd()
		{
			$cwd = getcwd();

			if (!is_string($cwd)) {
				throw new \CzProject\GitPhp\InvalidStateException('Getting of CWD failed.');
			}

			return $cwd;
		}


		/**
		 * @param  array
		 * @param  array|NULL
		 * @return string
		 */
		protected function processCommand(array $args, array $env = NULL)
		{
			$cmd = [];

			foreach ($args as $arg) {
				if (is_array($arg)) {
					foreach ($arg as $key => $value) {
						$_c = '';

						if (is_string($key)) {
							$_c = "$key ";
						}

						$cmd[] = $_c . escapeshellarg($value);
					}

				} elseif (is_scalar($arg) && !is_bool($arg)) {
					$cmd[] = escapeshellarg($arg);
				}
			}

			$envPrefix = '';

			if ($env !== NULL) {
				$isWindows = DIRECTORY_SEPARATOR === '\\';

				foreach ($env as $envVar => $envValue) {
					if ($isWindows) {
						$envPrefix .= 'set ' . $envVar . '=' . $envValue . ' && ';

					} else {
						$envPrefix .= $envVar . '=' . $envValue . ' ';
					}
				}
			}

			return $envPrefix . $this->gitBinary . ' ' . implode(' ', $cmd);
		}
	}
