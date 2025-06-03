<?php

	declare(strict_types=1);

	namespace CzProject\GitPhp;


	class CommandProcessor
	{
		const MODE_DETECT = 0;
		const MODE_WINDOWS = 1;
		const MODE_NON_WINDOWS = 2;

		/** @var bool */
		private $isWindows;


		/**
		 * @param int $mode
		 */
		public function __construct($mode = self::MODE_DETECT)
		{
			if ($mode === self::MODE_NON_WINDOWS) {
				$this->isWindows = FALSE;

			} elseif ($mode === self::MODE_WINDOWS) {
				$this->isWindows = TRUE;

			} elseif ($mode === self::MODE_DETECT) {
				$this->isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

			} else {
				throw new InvalidArgumentException("Invalid mode '$mode'.");
			}
		}


		/**
		 * @param  string $app
		 * @param  array<mixed> $args
		 * @param  array<string, scalar>|NULL $env
		 * @return string
		 */
		public function process($app, array $args, ?array $env = NULL)
		{
			$cmd = [];

			foreach ($args as $arg) {
				if (is_array($arg)) {
					foreach ($arg as $key => $value) {
						$_c = '';

						if (is_string($key)) {
							$_c = "$key ";
						}

						if (is_bool($value)) {
							$value = $value ? '1' : '0';

						} elseif ($value instanceof CommitId) {
							$value = $value->toString();

						} elseif ($value === NULL) {
							// ignored
							continue;

						} elseif (!is_scalar($value)) {
							throw new InvalidStateException('Unknow option value type ' . (is_object($value) ? get_class($value) : gettype($value)) . '.');
						}

						$cmd[] = $_c . $this->escapeArgument((string) $value);
					}

				} elseif (is_scalar($arg) && !is_bool($arg)) {
					$cmd[] = $this->escapeArgument((string) $arg);

				} elseif ($arg === NULL) {
					// ignored

				} elseif ($arg instanceof CommitId) {
					$cmd[] = $arg->toString();

				} else {
					throw new InvalidStateException('Unknow argument type ' . (is_object($arg) ? get_class($arg) : gettype($arg)) . '.');
				}
			}

			$envPrefix = '';

			if ($env !== NULL) {
				foreach ($env as $envVar => $envValue) {
					if ($this->isWindows) {
						$envPrefix .= 'set ' . $envVar . '=' . $envValue . ' && ';

					} else {
						$envPrefix .= $envVar . '=' . $envValue . ' ';
					}
				}
			}

			return $envPrefix . $app . ' ' . implode(' ', $cmd);
		}


		/**
		 * @param  string $value
		 * @return string
		 */
		private function escapeArgument($value)
		{
			// inspired by Nette Tester
			if (preg_match('#^[a-z0-9._-]+\z#i', $value)) {
				return $value;
			}

			if ($this->isWindows) {
				return '"' . str_replace('"', '""', $value) . '"';
			}

			return escapeshellarg($value);
		}
	}
