<?php

	namespace CzProject\GitPhp;


	class RunnerResult
	{
		/** @var string */
		private $command;

		/** @var int */
		private $exitCode;

		/** @var string[] */
		private $output;

		/** @var string[] */
		private $errorOutput;


		/**
		 * @param  string $command
		 * @param  int $exitCode
		 * @param  string[] $output
		 * @param  string[] $errorOutput
		 */
		public function __construct($command, $exitCode, array $output, array $errorOutput)
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
			return $this->output;
		}


		/**
		 * @return string
		 */
		public function getOutputAsString()
		{
			return implode("\n", $this->output);
		}


		/**
		 * @return string|NULL
		 */
		public function getOutputLastLine()
		{
			$lastLine = end($this->output);
			return is_string($lastLine) ? $lastLine : NULL;
		}


		/**
		 * @return bool
		 */
		public function hasOutput()
		{
			return !empty($this->output);
		}


		/**
		 * @return string[]
		 */
		public function getErrorOutput()
		{
			return $this->errorOutput;
		}


		/**
		 * @return bool
		 */
		public function hasErrorOutput()
		{
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
	}
