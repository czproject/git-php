<?php

	namespace CzProject\GitPhp\Runners;

	use CzProject\GitPhp\CommandProcessor;
	use CzProject\GitPhp\GitException;
	use CzProject\GitPhp\IRunner;
	use CzProject\GitPhp\RunnerResult;


	class CliRunner implements IRunner
	{
		/** @var string */
		private $gitBinary;

		/** @var bool */
		private $oldGit = FALSE;

		/** @var CommandProcessor */
		private $commandProcessor;

		/**
		 * @param string $str
		 *
		 * @return string
		 */
		private function getVersion($str = '') {
			preg_match('/(?:version|v)\s*((?:[0-9]+\.?)+)/i', $str, $matches);
			return $matches[1];
		}


		/**
		 * @param  string $gitBinary
		 */
		public function __construct($gitBinary = 'git')
		{
			$this->gitBinary = $gitBinary;
			$this->commandProcessor = new CommandProcessor;

			$gitVersionResult = $this->run($this->getCwd(), ['version']);
			if ($gitVersionResult->isOk()
				&& (($gitVersionStr = $gitVersionResult->getOutputLastLine()) !== NULL)
				&& (version_compare($this->getVersion($gitVersionStr), '2.24') < 0)) {
					$this->oldGit = TRUE;
			}
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

			if ($this->oldGit && ($key = array_search('--end-of-options', $args)) !== FALSE) {
				unset($args[$key]);
			}

			$pipes = [];
			$command = $this->commandProcessor->process($this->gitBinary, $args);
			$process = proc_open($command, $descriptorspec, $pipes, $cwd, $env, [
				'bypass_shell' => TRUE,
			]);

			if (!$process) {
				throw new GitException("Executing of command '$command' failed (directory $cwd).");
			}

			// Reset output and error
			stream_set_blocking($pipes[1], FALSE);
			stream_set_blocking($pipes[2], FALSE);
			$stdout = '';
			$stderr = '';

			while (TRUE) {
				// Read standard output
				$stdoutOutput = stream_get_contents($pipes[1]);

				if (is_string($stdoutOutput)) {
					$stdout .= $stdoutOutput;
				}

				// Read error output
				$stderrOutput = stream_get_contents($pipes[2]);

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
		 * @param  string $output
		 * @return string[]
		 */
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
