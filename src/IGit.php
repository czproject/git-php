<?php
	/**
	 * IGit interface
	 *
	 * @author  Jan Pecha, <janpecha@email.cz>
	 * @license New BSD License (BSD-3), see file license.md
	 */

	namespace Cz\Git;

	interface IGit
	{
		/**
		 * Creates a tag.
		 * @param  string
		 * @param  array|NULL
		 * @throws GitException
		 */
		function createTag($name, $options = NULL);


		/**
		 * Removes tag.
		 * @param  string
		 * @throws GitException
		 */
		function removeTag($name);


		/**
		 * Renames tag.
		 * @param  string
		 * @param  string
		 * @throws GitException
		 */
		function renameTag($oldName, $newName);


		/**
		 * Returns list of tags in repo.
		 * @return string[]|NULL  NULL => no tags
		 */
		function getTags();


		/**
		 * Merges branches.
		 * @param  string
		 * @param  array|NULL
		 * @throws GitException
		 */
		function merge($branch, $options = NULL);


		/**
		 * Creates new branch.
		 * @param  string
		 * @param  bool
		 * @throws GitException
		 */
		function createBranch($name, $checkout = FALSE);


		/**
		 * Removes branch.
		 * @param  string
		 * @throws GitException
		 */
		function removeBranch($name);


		/**
		 * Gets name of current branch
		 * @return string
		 * @throws GitException
		 */
		function getCurrentBranchName();


		/**
		 * Returns list of branches in repo.
		 * @return string[]|NULL  NULL => no branches
		 */
		function getBranches();


		/**
		 * Returns list of local branches in repo.
		 * @return string[]|NULL  NULL => no branches
		 */
		function getLocalBranches();


		/**
		 * Checkout branch.
		 * @param  string
		 * @throws GitException
		 */
		function checkout($name);


		/**
		 * Removes file(s).
		 * @param  string|string[]
		 * @throws GitException
		 */
		function removeFile($file);


		/**
		 * Adds file(s).
		 * @param  string|string[]
		 * @throws GitException
		 */
		function addFile($file);


		/**
		 * Adds all created, modified & removed files.
		 * @throws GitException
		 */
		function addAllChanges();


		/**
		 * Renames file(s).
		 * @param  string|string[]  from: array('from' => 'to', ...) || (from, to)
		 * @param  string|NULL
		 * @throws GitException
		 */
		function renameFile($file, $to = NULL);


		/**
		 * Commits changes
		 * @param  string
		 * @param  string[]  param => value
		 * @throws GitException
		 */
		function commit($message, $params = NULL);


		/**
		 * Exists changes?
		 * @return bool
		 */
		function hasChanges();


		/**
		 * Pull changes from a remote
		 * @param  string|NULL
		 * @param  array|NULL
		 * @return self
		 * @throws GitException
		 */
		function pull($remote = NULL, array $params = NULL);


		/**
		 * Push changes to a remote
		 * @param  string|NULL
		 * @param  array|NULL
		 * @return self
		 * @throws GitException
		 */
		function push($remote = NULL, array $params = NULL);


		/**
		 * Run fetch command to get latest branches
		 * @param  string|NULL
		 * @param  array|NULL
		 * @return self
		 * @throws GitException
		 */
		function fetch($remote = NULL, array $params = NULL);

		/**
		 * Adds new remote repository
		 * @param  string
		 * @param  string
		 * @param  array|NULL
		 * @return self
		 */
		function addRemote($name, $url, array $params = NULL);


		/**
		 * Renames remote repository
		 * @param  string
		 * @param  string
		 * @return self
		 */
		function renameRemote($oldName, $newName);


		/**
		 * Removes remote repository
		 * @param  string
		 * @return self
		 */
		function removeRemote($name);


		/**
		 * Changes remote repository URL
		 * @param  string
		 * @param  string
		 * @param  array|NULL
		 * @return self
		 */
		function setRemoteUrl($name, $url, array $params = NULL);


		/**
		 * Init repo in directory
		 * @param  string
		 * @param  array|NULL
		 * @return self
		 * @throws GitException
		 */
		static function init($directory, array $params = NULL);


		/**
		 * Clones GIT repository from $url into $directory
		 * @param  string
		 * @param  string|NULL
		 * @return self
		 */
		static function cloneRepository($url, $directory = NULL);
	}


	class GitException extends \Exception
	{
		/**
		 * @param string $message A description about which particular command (with parameters) has failed.
		 * @param int $code A numeric exit code from the failed git command (if applicable).
		 * @param \Throwable|NULL $previous
		 * @param string[]|string $command_output stdout/stderr output text from exec(). Exec() passes an array of strings to the $output variable, but you can also pass a simple string value if you wish. If this is an array, the elements of the array will be joined to a single string using PHP_EOL as the separator.
		 */
		public function __construct($message = "", $code = 0, \Throwable $previous = null, $command_output='')
		{
			if ($command_output) $message .= PHP_EOL . 'Command output: ' . PHP_EOL . static::array_to_string($command_output);
			
			parent::__construct($message, $code, $previous);
		}
		
		private static function array_to_string($array_or_string)
		{
			if (!is_array($array_or_string)) return $array_or_string;
			return implode(PHP_EOL, $array_or_string);
		}
	}
