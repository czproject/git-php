<?php
	/** Default Implementation of IGit Interface
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 */
	
	namespace Cz\Git;
	
	class GitRepository implements IGit
	{
		/** @var  string */
		private $repository;
		
		/** @var  string|NULL  @internal */
		private $cwd;
		
		
		
		public function __construct($repository)
		{
			if(basename($repository) === '.git')
			{
				$repository = dirname($repository);
			}
			
			$this->repository = realpath($repository);
			
			if($this->repository === FALSE)
			{
				throw new GitException("Repository '$repository' not found.");
			}
		}
		
		
		
		/**
		 * Creates a tag.
		 * @param	string
		 * @throws	Cz\Git\GitException
		 * @return	self
		 */
		public function createTag($name)
		{
			return $this->begin()
				->run('git tag', $name)
				->end();
		}
		
		
		
		/**
		 * Removes tag.
		 * @param	string
		 * @throws	Cz\Git\GitException
		 * @return	self
		 */
		public function removeTag($name)
		{
			return $this->begin()
				->run('git tag', array(
					'-d' => $name,
				))
				->end();
		}
		
		
		
		/**
		 * Renames tag.
		 * @param	string
		 * @param	string
		 * @throws	Cz\Git\GitException
		 * @return	self
		 */
		public function renameTag($oldName, $newName)
		{
			return $this->begin()
				// http://stackoverflow.com/a/1873932
				// create new as alias to old (`git tag NEW OLD`)
				->run('git tag', $newName, $oldName)
				// delete old (`git tag -d OLD`)
				->removeTag($oldName) // WARN! removeTag() calls end() method!!!
				->end();
		}
		
		
		
		/**
		 * Merges branches.
		 * @param	string
		 * @param	array|NULL
		 * @throws	Cz\Git\GitException
		 * @return	self
		 */
		public function merge($branch, $options = NULL)
		{
			return $this->begin()
				->run('git merge', $options, $branch)
				->end();
		}
		
		
		
		/**
		 * Creates new branch.
		 * @param	string
		 * @param	bool
		 * @throws	Cz\Git\GitException
		 * @return	self
		 */
		public function createBranch($name, $checkout = FALSE)
		{
			$this->begin();
			
			// git branch $name
			$this->run('git branch', $name);
			
			if($checkout)
			{
				$this->checkout($name);
			}
			
			return $this->end();
		}
		
		
		
		/**
		 * Removes branch.
		 * @param	string
		 * @throws	Cz\Git\GitException
		 * @return	self
		 */
		public function removeBranch($name)
		{
			return $this->begin()
				->run('git branch', array(
					'-d' => $name,
				))
				->end();
		}
		
		
		
		/**
		 * Gets name of current branch
		 * @return	string
		 * @throws	Cz\Git\GitException
		 */
		public function getCurrentBranchName()
		{
			$output = array();
			$exitCode = NULL;
			
			$this->begin();
			exec('git branch', $output, $exitCode);
			$this->end();
			
			if($exitCode === 0 && is_array($output))
			{
				foreach($output as $line)
				{
					if($line[0] === '*')
					{
						return trim(substr($line, 1));
					}
				}
			}
			
			throw new GitException('Getting current branch name failed.');
		}
		
		
		
		/**
		 * Checkout branch.
		 * @param	string
		 * @throws	Cz\Git\GitException
		 * @return	self
		 */
		public function checkout($name)
		{
			return $this->begin()
				->run('git checkout', $name)
				->end();
		}
		
		
		
		/**
		 * Removes file(s).
		 * @param	string|string[]
		 * @throws	Cz\Git\GitException
		 * @return	self
		 */
		public function removeFile($file)
		{
			if(!is_array($file))
			{
				$file = func_get_args();
			}
			
			$this->begin();
			
			foreach($file as $item)
			{
				$this->run('git rm', $item, '-r');
			}
			
			return $this->end();
		}
		
		
		
		/**
		 * Adds file(s).
		 * @param	string|string[]
		 * @throws	Cz\Git\GitException
		 * @return	self
		 */
		public function addFile($file)
		{
			if(!is_array($file))
			{
				$file = func_get_args();
			}
			
			$this->begin();
			
			foreach($file as $item)
			{
				$this->run('git add', $item);
			}
			
			return $this->end();
		}
		
		
		
		/**
		 * Renames file(s).
		 * @param	string|string[]  from: array('from' => 'to', ...) || (from, to)
		 * @param	string|NULL
		 * @throws	Cz\Git\GitException
		 * @return	self
		 */
		public function renameFile($file, $to = NULL)
		{
			if(!is_array($file)) // rename(file, to);
			{
				$from[$file] = $to;
				$file = $from;
				unset($from);
			}
			
			$this->begin();
			
			foreach($file as $from => $to)
			{
				$this->run('git mv', $from, $to);
			}
			
			return $this->end();
		}
		
		
		
		/**
		 * Commits changes
		 * @param	string
		 * @param	string[]  param => value
		 * @throws	Cz\Git\GitException
		 * @return	self
		 */
		public function commit($message, $params = NULL)
		{
			if(!is_array($params))
			{
				$params = array();
			}
			
			return $this->begin()
				->run("git commit", $params, array(
					'-m' => $message,
				))
				->end();
		}
		
		
		
		/**
		 * Exists changes?
		 * @return	bool
		 */
		public function isChanges()
		{
			$this->begin();
			$lastLine = exec('git status');
			$this->end();
			return (strpos($lastLine, 'nothing to commit')) === FALSE; // FALSE => changes
		}
		
		
		
		/**
		 * @return	self
		 */
		private function begin()
		{
			if($this->cwd === NULL) // TODO: good idea??
			{
				$this->cwd = getcwd();
				chdir($this->repository);
			}
			
			return $this;
		}
		
		
		
		/**
		 * @return	self
		 */
		private function end()
		{
			if(is_string($this->cwd))
			{
				chdir($this->cwd);
			}
			
			$this->cwd = NULL;
			return $this;
		}
		
		
		
		/** Runs command.
		 * @param	string|array
		 * @return	self
		 * @throws	Cz\Git\GitException
		 */
		protected function run($cmd/*, $options = NULL*/)
		{
			$args = func_get_args();
			$cmd = array();
			
			$programName = array_shift($args);
			
			foreach($args as $arg)
			{
				if(is_array($arg))
				{
					foreach($arg as $key => $value)
					{
						$_c = '';
						
						if(is_string($key))
						{
							$_c = "$key ";
						}
						
						$cmd[] = $_c . escapeshellarg($value);
					}
				}
				elseif(is_scalar($arg) && !is_bool($arg))
				{
					$cmd[] = escapeshellarg($arg);
				}
			}
			
			$cmd = "$programName " . implode(' ', $cmd);
			$success = system($cmd, $ret);
			
			if($success === FALSE || $ret !== 0)
			{
				throw new GitException("Command '$cmd' failed.");
			}
			
			return $this;
		}
	}

