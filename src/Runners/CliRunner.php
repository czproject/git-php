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

			$descriptorspec = [
				0 => ['pipe', 'r'], // stdin
				1 => ['pipe', 'w'], // stdout
				2 => ['pipe', 'w'], // stderr
			];

			$pipes = [];
			$command = $this->processCommand($args);
			$process = proc_open($command, $descriptorspec, $pipes, $cwd, $env);

			if (!$process) {
				throw new GitException("Executing of command '$command' failed (directory $cwd).");
			}

			// Reset output and error
			$stdout = '';
			$stderr = '';

			while (TRUE) {
				// Read standard output
				$stdoutOutput = fgets($pipes[1], 1024);

				if (is_string($stdoutOutput)) {
					$stdout .= $stdoutOutput;
				}

				// Read error output
				$stderrOutput = fgets($pipes[2], 1024);

				if (is_string($stderrOutput)) {
					$stderr .= $stderrOutput;
				}

				// We are done
				if ((feof($pipes[1]) || $stdoutOutput === FALSE) && (feof($pipes[2]) || $stderrOutput === FALSE)) {
					break;
				}
			}

			$returnCode = proc_close($process);
			return new RunnerResult($command, $returnCode, $this->convertOutput($stdout), $this->convertOutput($stderr));
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


		protected function convertOutput($output)
		{
			$output = str_replace(["\r\n", "\r"], "\n", $output);
			$output = rtrim($output, "\n");

			if ($output === '') {
				return [];
			}

			return explode("\n", $output);
		}
	}
