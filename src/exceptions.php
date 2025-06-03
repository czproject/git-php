<?php

	declare(strict_types=1);

	namespace CzProject\GitPhp;


	class Exception extends \Exception
	{
	}


	class GitException extends Exception
	{
		/** @var RunnerResult|NULL */
		private $runnerResult;


		/**
		 * @param string $message
		 * @param int $code
		 */
		public function __construct($message, $code = 0, ?\Exception $previous = NULL, ?RunnerResult $runnerResult = NULL)
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


	class InvalidArgumentException extends Exception
	{
	}


	class InvalidStateException extends Exception
	{
	}


	class StaticClassException extends Exception
	{
	}
