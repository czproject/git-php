<?php

	declare(strict_types=1);

	namespace CzProject\GitPhp;


	class Helpers
	{
		public function __construct()
		{
			throw new StaticClassException('This is static class.');
		}


		/**
		 * Is path absolute?
		 * Method from Nette\Utils\FileSystem
		 * @link   https://github.com/nette/nette/blob/master/Nette/Utils/FileSystem.php
		 * @param  string $path
		 * @return bool
		 */
		public static function isAbsolute($path)
		{
			return (bool) preg_match('#[/\\\\]|[a-zA-Z]:[/\\\\]|[a-z][a-z0-9+.-]*://#Ai', $path);
		}


		/**
		 * @param  string $url  /path/to/repo.git | host.xz:foo/.git | ...
		 * @return string  repo | foo | ...
		 */
		public static function extractRepositoryNameFromUrl($url)
		{
			// /path/to/repo.git => repo
			// host.xz:foo/.git => foo
			$directory = rtrim($url, '/');

			if (substr($directory, -5) === '/.git') {
				$directory = substr($directory, 0, -5);
			}

			$directory = basename($directory, '.git');

			if (($pos = strrpos($directory, ':')) !== FALSE) {
				$directory = substr($directory, $pos + 1);
			}

			return $directory;
		}
	}
