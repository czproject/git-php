<?php

	declare(strict_types=1);

	namespace CzProject\GitPhp\Tests;

	use CzProject\GitPhp\CommandProcessor;
	use CzProject\GitPhp\GitException;
	use CzProject\GitPhp\IRunner;
	use CzProject\GitPhp\RunnerResult;


	class AssertRunner implements \CzProject\GitPhp\IRunner
	{
		/** @var string */
		private $cwd;

		/** @var CommandProcessor */
		private $commandProcessor;

		/** @var RunnerResult[] */
		private $asserts = [];


		/**
		 * @param  string $cwd
		 */
		public function __construct($cwd)
		{
			$this->cwd = $cwd;
			$this->commandProcessor = new CommandProcessor;
		}


		/**
		 * @param  mixed[] $expectedArgs
		 * @param  array<string, scalar> $expectedEnv
		 * @param  string[] $resultOutput
		 * @param  string[] $resultErrorOutput
		 * @param  int $resultExitCode
		 * @return self
		 */
		public function assert(array $expectedArgs, array $expectedEnv = [], array $resultOutput = [], array $resultErrorOutput = [], $resultExitCode = 0)
		{
			$cmd = $this->commandProcessor->process('git', $expectedArgs, $expectedEnv);
			$this->asserts[] = new RunnerResult($cmd, $resultExitCode, $resultOutput, $resultErrorOutput);
			return $this;
		}


		/**
		 * @return self
		 */
		public function resetAsserts()
		{
			$this->asserts = [];
			return $this;
		}


		/**
		 * @return RunnerResult
		 */
		public function run($cwd, array $args, ?array $env = NULL)
		{
			if (empty($this->asserts)) {
				throw new \CzProject\GitPhp\InvalidStateException('Missing asserts, use $runner->assert().');
			}

			$cmd = $this->commandProcessor->process('git', $args, $env);
			$result = current($this->asserts);

			if (!($result instanceof RunnerResult)) {
				throw new \CzProject\GitPhp\InvalidStateException("Missing assert for command '$cmd'");
			}

			\Tester\Assert::same($result->getCommand(), $cmd);
			next($this->asserts);
			return $result;
		}


		/**
		 * @return string
		 */
		public function getCwd()
		{
			return $this->cwd;
		}
	}
