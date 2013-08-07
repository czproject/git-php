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
		public function createTag($name);
		
		
		
		/**
		 * Removes tag.
		 * @param	string
		 * @throws	Cz\Git\GitException
		 */
		public function removeTag($name);
		
		
		
		/**
		 * Renames tag.
		 * @param	string
		 * @param	string
		 * @throws	Cz\Git\GitException
		 */
		public function renameTag($oldName, $newName);
		
		
		
		/**
		 * Merges branches.
		 * @param	string
		 * @param	array|NULL
		 * @throws	Cz\Git\GitException
		 */
		public function merge($branch, $options = NULL);
		
		
		
		/**
		 * Creates new branch.
		 * @param	string
		 * @param	bool
		 * @throws	Cz\Git\GitException
		 */
		public function createBranch($name, $checkout = FALSE);
		
		
		
		/**
		 * Removes branch.
		 * @param	string
		 * @throws	Cz\Git\GitException
		 */
		public function removeBranch($name);
		
		
		
		/**
		 * Gets name of current branch
		 * @return	string
		 * @throws	Cz\Git\GitException
		 */
		public function getCurrentBranchName();
		
		
		
		/**
		 * Checkout branch.
		 * @param	string
		 * @throws	Cz\Git\GitException
		 */
		public function checkout($name);
		
		
		
		/**
		 * Removes file(s).
		 * @param	string|string[]
		 * @throws	Cz\Git\GitException
		 */
		public function removeFile($file);
		
		
		
		/**
		 * Adds file(s).
		 * @param	string|string[]
		 * @throws	Cz\Git\GitException
		 */
		public function addFile($file);
		
		
		
		/**
		 * Renames file(s).
		 * @param	string|string[]  from: array('from' => 'to', ...) || (from, to)
		 * @param	string|NULL
		 * @throws	Cz\Git\GitException
		 */
		public function renameFile($file, $to = NULL);
		
		
		
		/**
		 * Commits changes
		 * @param	string
		 * @param	string[]  param => value
		 * @throws	Cz\Git\GitException
		 */
		public function commit($message, $params = NULL);
		
		
		
		/**
		 * Exists changes?
		 * @return	bool
		 */
		public function isChanges();
	}
	
	
	
	class GitException extends \Exception
	{
	}

