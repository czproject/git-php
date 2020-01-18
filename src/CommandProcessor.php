<?php

	namespace CzProject\GitPhp;


	class CommandProcessor
	{
		const MODE_DETECT = 0;
		const MODE_WINDOWS = 1;
		const MODE_NON_WINDOWS = 2;

		/** @var bool */
		private $isWindows;


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
		 * @param  string
		 * @param  array
		 * @param  array|NULL
		 * @return string
		 */
		public function process($app, array $args, array $env = NULL)
		{
			$cmd = [];

			foreach ($args as $arg) {
				if (is_array($arg)) {
					foreach ($arg as $key => $value) {
						$_c = '';

						if (is_string($key)) {
							$_c = "$key ";
						}

						$cmd[] = $_c . $this->escapeArgument($value);
					}

				} elseif (is_scalar($arg) && !is_bool($arg)) {
					$cmd[] = $this->escapeArgument($arg);
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
		 * @param  string
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
