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
		 * @return	string
		 */
		public function getRepositoryPath()
		{
			return $this->repository;
		}
		
		
		
		/**
		 * Creates a tag.
		 * `git tag <name>`
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
		 * `git tag -d <name>`
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
		 * `git tag <new> <old>`
		 * `git tag -d <old>`
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
		 * Returns list of tags in repo.
		 * @return	string[]|NULL  NULL => no tags
		 */
		public function getTags()
		{
			return $this->extractFromCommand('git tag', 'trim');
		}
		
		
		
		/**
		 * Merges branches.
		 * `git merge <options> <name>`
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
		 * `git branch <name>`
		 * (optionaly) `git checkout <name>`
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
		 * `git branch -d <name>`
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
		 * `git branch` + magic
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
		 * Returns list of branches in repo.
		 * @return	string[]|NULL  NULL => no branches
		 */
		public function getBranches()
		{
			return $this->extractFromCommand('git branch', function($value) {
				return trim(substr($value, 1));
			});
		}
		
		
		
		/**
		 * Checkout branch.
		 * `git checkout <branch>`
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
		 * `git rm <file>`
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
		 * `git add <file>`
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
				// TODO: ?? is file($repo . / . $item) ??
				$this->run('git add', $item);
			}
			
			return $this->end();
		}
		
		
		
		/**
		 * Renames file(s).
		 * `git mv <file>`
		 * @param	string|string[]  from: array('from' => 'to', ...) || (from, to)
		 * @param	string|NULL
		 * @throws	Cz\Git\GitException
		 * @return	self
		 */
		public function renameFile($file, $to = NULL)
		{
			if(!is_array($file)) // rename(file, to);
			{
				$file = array(
					$file => $to,
				);
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
		 * `git commit <params> -m <message>`
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
		 * `git status` + magic
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
		
		
		
		/**
		 * @param	string
		 * @param	callback|NULL
		 * @return	string[]|NULL
		 */
		private function extractFromCommand($cmd, $filter = NULL)
		{
			$output = array();
			$exitCode = NULL;
			
			$this->begin();
			exec("$cmd", $output, $exitCode);
			$this->end();
			
			if($exitCode !== 0 || !is_array($output))
			{
				throw new GitException("Command $cmd failed.");
			}
			
			if($filter !== NULL)
			{
				$newArray = array();
				
				foreach($output as $line)
				{
					$value = $filter($line);
					
					if($value === FALSE)
					{
						continue;
					}
					
					$newArray[] = $value;
				}
				
				$output = $newArray;
			}
			
			if(!isset($output[0])) // empty array
			{
				return NULL;
			}
			
			return $output;
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
			exec($cmd, $output, $ret);
			
			if($ret !== 0)
			{
				throw new GitException("Command '$cmd' failed.");
			}
			
			return $this;
		}
		
		
		
		/**
		 * Init repo in directory
		 * @param	string
		 * @return	self
		 * @throws	GitException
		 */
		public static function init($directory)
		{
			if(is_dir("$directory/.git"))
			{
				throw new GitException("Repo already exists in $directory.");
			}
			
			if(!is_dir($directory) && !@mkdir($directory, 0777, TRUE)) // intentionally @; not atomic; from Nette FW
			{
				throw new GitException("Unable to create directory '$directory'.");
			}
			
			$cwd = getcwd();
			chdir($directory);
			exec('git init', $output, $returnCode);
			
			if($returnCode !== 0)
			{
				throw new GitException("Git init failed (directory $directory).");
			}
			
			$repo = getcwd();
			chdir($cwd);
			
			return new static($repo);
		}
	}

