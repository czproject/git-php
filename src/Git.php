<?php

	declare(strict_types=1);

	namespace CzProject\GitPhp;


	class Git
	{
		/** @var IRunner */
		protected $runner;


		public function  __construct(?IRunner $runner = NULL)
		{
			$this->runner = $runner !== NULL ? $runner : new Runners\CliRunner;
		}


		/**
		 * @param  string $directory
		 * @return GitRepository
		 */
		public function open($directory)
		{
			return new GitRepository($directory, $this->runner);
		}


		/**
		 * Init repo in directory
		 * @param  string $directory
		 * @param  array<mixed>|NULL $params
		 * @return GitRepository
		 * @throws GitException
		 */
		public function init($directory, ?array $params = NULL)
		{
			if (is_dir("$directory/.git")) {
				throw new GitException("Repo already exists in $directory.");
			}

			if (!is_dir($directory) && !@mkdir($directory, 0777, TRUE)) { // intentionally @; not atomic; from Nette FW
				throw new GitException("Unable to create directory '$directory'.");
			}

			try {
				$this->run($directory, [
					'init',
					$params,
					'--end-of-options',
					$directory
				]);

			} catch (GitException $e) {
				throw new GitException("Git init failed (directory $directory).", $e->getCode(), $e);
			}

			return $this->open($directory);
		}


		/**
		 * Clones GIT repository from $url into $directory
		 * @param  string $url
		 * @param  string|NULL $directory
		 * @param  array<mixed>|NULL $params
		 * @return GitRepository
		 * @throws GitException
		 */
		public function cloneRepository($url, $directory = NULL, ?array $params = NULL)
		{
			if ($directory !== NULL && is_dir("$directory/.git")) {
				throw new GitException("Repo already exists in $directory.");
			}

			$cwd = $this->runner->getCwd();

			if ($directory === NULL) {
				$directory = Helpers::extractRepositoryNameFromUrl($url);
				$directory = "$cwd/$directory";

			} elseif(!Helpers::isAbsolute($directory)) {
				$directory = "$cwd/$directory";
			}

			if ($params === NULL) {
				$params = '-q';
			}

			try {
				$this->run($cwd, [
					'clone',
					$params,
					'--end-of-options',
					$url,
					$directory
				]);

			} catch (GitException $e) {
				$stderr = '';
				$result = $e->getRunnerResult();

				if ($result !== NULL && $result->hasErrorOutput()) {
					$stderr = implode(PHP_EOL, $result->getErrorOutput());
				}

				throw new GitException("Git clone failed (directory $directory)." . ($stderr !== '' ? ("\n$stderr") : ''));
			}

			return $this->open($directory);
		}


		/**
		 * @param  string $url
		 * @param  array<string>|NULL $refs
		 * @return bool
		 */
		public function isRemoteUrlReadable($url, ?array $refs = NULL)
		{
			$result = $this->runner->run($this->runner->getCwd(), [
				'ls-remote',
				'--heads',
				'--quiet',
				'--end-of-options',
				$url,
				$refs,
			], [
				'GIT_TERMINAL_PROMPT' => 0,
			]);

			return $result->isOk();
		}


		/**
		 * @param  string $cwd
		 * @param  array<mixed> $args
		 * @param  array<string, scalar> $env
		 * @return RunnerResult
		 * @throws GitException
		 */
		private function run($cwd, array $args, ?array $env = NULL)
		{
			$result = $this->runner->run($cwd, $args, $env);

			if (!$result->isOk()) {
				throw new GitException("Command '{$result->getCommand()}' failed (exit-code {$result->getExitCode()}).", $result->getExitCode(), NULL, $result);
			}

			return $result;
		}
	}
