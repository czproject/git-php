<?php

	declare(strict_types=1);

	namespace CzProject\GitPhp;


	class RunnerResult
	{
		/** @var string */
		private $command;

		/** @var int */
		private $exitCode;

		/** @var string|string[] */
		private $output;

		/** @var string|string[] */
		private $errorOutput;


		/**
		 * @param  string $command
		 * @param  int $exitCode
		 * @param  string|string[] $output
		 * @param  string|string[] $errorOutput
		 */
		public function __construct($command, $exitCode, $output, $errorOutput)
		{
			$this->command = (string) $command;
			$this->exitCode = (int) $exitCode;
			$this->output = $output;
			$this->errorOutput = $errorOutput;
		}


		/**
		 * @return bool
		 */
		public function isOk()
		{
			return $this->exitCode === 0;
		}


		/**
		 * @return string
		 */
		public function getCommand()
		{
			return $this->command;
		}


		/**
		 * @return int
		 */
		public function getExitCode()
		{
			return $this->exitCode;
		}


		/**
		 * @return string[]
		 */
		public function getOutput()
		{
			if (is_string($this->output)) {
				return $this->splitOutput($this->output);
			}

			return $this->output;
		}


		/**
		 * @return string
		 */
		public function getOutputAsString()
		{
			if (is_string($this->output)) {
				return $this->output;
			}

			return implode("\n", $this->output);
		}


		/**
		 * @return string|NULL
		 */
		public function getOutputLastLine()
		{
			$output = $this->getOutput();
			$lastLine = end($output);
			return is_string($lastLine) ? $lastLine : NULL;
		}


		/**
		 * @return bool
		 */
		public function hasOutput()
		{
			if (is_string($this->output)) {
				return trim($this->output) !== '';
			}

			return !empty($this->output);
		}


		/**
		 * @return string[]
		 */
		public function getErrorOutput()
		{
			if (is_string($this->errorOutput)) {
				return $this->splitOutput($this->errorOutput);
			}

			return $this->errorOutput;
		}


		/**
		 * @return string
		 */
		public function getErrorOutputAsString()
		{
			if (is_string($this->errorOutput)) {
				return $this->errorOutput;
			}

			return implode("\n", $this->errorOutput);
		}


		/**
		 * @return bool
		 */
		public function hasErrorOutput()
		{
			if (is_string($this->errorOutput)) {
				return trim($this->errorOutput) !== '';
			}

			return !empty($this->errorOutput);
		}


		/**
		 * @return string
		 */
		public function toText()
		{
			return '$ ' . $this->getCommand() . "\n\n"
				. "---- STDOUT: \n\n"
				. implode("\n", $this->getOutput()) . "\n\n"
				. "---- STDERR: \n\n"
				. implode("\n", $this->getErrorOutput()) . "\n\n"
				. '=> ' . $this->getExitCode() . "\n\n";
		}


		/**
		 * @param  string $output
		 * @return string[]
		 */
		private function splitOutput($output)
		{
			$output = str_replace(["\r\n", "\r"], "\n", $output);
			$output = rtrim($output, "\n");

			if ($output === '') {
				return [];
			}

			return explode("\n", $output);
		}
	}
