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
		public function tag($name);
		
		
		
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
		public function branchCreate($name, $checkout = FALSE);
		
		
		
		/**
		 * Removes branch.
		 * @param	string
		 * @throws	Cz\Git\GitException
		 */
		public function branchRemove($name);
		
		
		
		/**
		 * Gets name of current branch
		 * @return	string
		 * @throws	Cz\Git\GitException
		 */
		public function branchName();
		
		
		
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
		public function remove($file);
		
		
		
		/**
		 * Adds file(s).
		 * @param	string|string[]
		 * @throws	Cz\Git\GitException
		 */
		public function add($file);
		
		
		
		/**
		 * Renames file(s).
		 * @param	string|string[]  from: array('from' => 'to', ...) || (from, to)
		 * @param	string|NULL
		 * @throws	Cz\Git\GitException
		 */
		public function rename($file, $to = NULL);
		
		
		
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

