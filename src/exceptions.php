<?php

	namespace CzProject\GitPhp;


	class Exception extends \Exception
	{
	}


	class GitException extends Exception
	{
		/** @var RunnerResult|NULL */
		private $runnerResult;


		public function __construct($message, $code = 0, \Exception $previous = NULL, RunnerResult $runnerResult = NULL)
		{
			parent::__construct($message, $code, $previous);
			$this->runnerResult = $runnerResult;
		}


		/**
		 * @return RunnerResult|NULL
		 */
		public function getRunnerResult()
		{
			return $this->runnerResult;
		}
	}


	class InvalidStateException extends Exception
	{
	}


	class StaticClassException extends Exception
	{
	}
