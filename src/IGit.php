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
	}
