<?php
	/** IGit interface
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 */
	
	namespace Cz\Git;
	
	interface IGit
	{
		/**
		 * Creates a tag.
		 * @param	string
		 * @throws	Cz\Git\GitException
		 */
		function createTag($name);
		
		
		
		/**
		 * Removes tag.
		 * @param	string
		 * @throws	Cz\Git\GitException
		 */
		function removeTag($name);
		
		
		
		/**
		 * Renames tag.
		 * @param	string
		 * @param	string
		 * @throws	Cz\Git\GitException
		 */
		function renameTag($oldName, $newName);
		
		
		
		/**
		 * Returns list of tags in repo.
		 * @return	string[]|NULL  NULL => no tags
		 */
		function getTags();
		
		
		
		/**
		 * Merges branches.
		 * @param	string
		 * @param	array|NULL
		 * @throws	Cz\Git\GitException
		 */
		function merge($branch, $options = NULL);
		
		
		
		/**
		 * Creates new branch.
		 * @param	string
		 * @param	bool
		 * @throws	Cz\Git\GitException
		 */
		function createBranch($name, $checkout = FALSE);
		
		
		
		/**
		 * Removes branch.
		 * @param	string
		 * @throws	Cz\Git\GitException
		 */
		function removeBranch($name);
		
		
		
		/**
		 * Gets name of current branch
		 * @return	string
		 * @throws	Cz\Git\GitException
		 */
		function getCurrentBranchName();
		
		
		
		
		/**
		 * Returns list of branches in repo.
		 * @return	string[]|NULL  NULL => no branches
		 */
		function getBranches();
		
		
		
		/**
		 * Checkout branch.
		 * @param	string
		 * @throws	Cz\Git\GitException
		 */
		function checkout($name);
		
		
		
		/**
		 * Removes file(s).
		 * @param	string|string[]
		 * @throws	Cz\Git\GitException
		 */
		function removeFile($file);
		
		
		
		/**
		 * Adds file(s).
		 * @param	string|string[]
		 * @throws	Cz\Git\GitException
		 */
		function addFile($file);
		
		
		
		/**
		 * Renames file(s).
		 * @param	string|string[]  from: array('from' => 'to', ...) || (from, to)
		 * @param	string|NULL
		 * @throws	Cz\Git\GitException
		 */
		function renameFile($file, $to = NULL);
		
		
		
		/**
		 * Commits changes
		 * @param	string
		 * @param	string[]  param => value
		 * @throws	Cz\Git\GitException
		 */
		function commit($message, $params = NULL);
		
		
		
		/**
		 * Exists changes?
		 * @return	bool
		 */
		function isChanges();
	}
	
	
	
	class GitException extends \Exception
	{
	}

