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


		/**
		 * @param  string
		 * @param  int
		 * @param  string[]
		 */
		public function __construct($command, $exitCode, array $output)
		{
			$this->command = (string) $command;
			$this->exitCode = (int) $exitCode;
			$this->output = $output;
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
		 * @return string
		 */
		public function toText()
		{
			return '$ ' . $this->getCommand() . "\n\n"
				. implode("\n", $this->getOutput()) . "\n\n"
				. '=> ' . $this->getExitCode() . "\n\n";
		}
	}
