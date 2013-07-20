<?php
	/** Default Implementation of IGit Interface
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 */
	
	namespace Cz\Git;
	
	class Git implements IGit
	{
		/**
		 * Creates a tag.
		 * @param	string
		 * @throws	Cz\Git\GitException
		 * @return	self
		 */
		public function tag($name)
		{
			$this->run('git tag', $name);
			return $this;
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
			$this->run('git merge', $options, $branch);
			return $this;
		}
		
		
		
		/**
		 * Creates new branch.
		 * @param	string
		 * @param	bool
		 * @throws	Cz\Git\GitException
		 * @return	self
		 */
		public function branchCreate($name, $checkout = FALSE)
		{
			// git branch $name
			$this->run('git branch', $name);
			
			if($checkout)
			{
				$this->checkout($name);
			}
			
			return $this;
		}
		
		
		
		/**
		 * Removes branch.
		 * @param	string
		 * @throws	Cz\Git\GitException
		 * @return	self
		 */
		public function branchRemove($name)
		{
			$this->run('git branch', array(
				'-d' => $name,
			));
			return $this;
		}
		
		
		
		/**
		 * Gets name of current branch
		 * @return	string
		 * @throws	Cz\Git\GitException
		 */
		public function branchName()
		{
			$output = array();
			$exitCode = NULL;
			
			exec('git branch', $output, $exitCode);
			
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
			$this->run('git checkout', $name);
			return $this;
		}
		
		
		
		/**
		 * Removes file(s).
		 * @param	string|string[]
		 * @throws	Cz\Git\GitException
		 * @return	self
		 */
		public function remove($file)
		{
			if(!is_array($file))
			{
				$file = func_get_args();
			}
			
			foreach($file as $item)
			{
				$this->run('git rm', $item, '-r');
			}
			
			return $this;
		}
		
		
		
		/**
		 * Adds file(s).
		 * @param	string|string[]
		 * @throws	Cz\Git\GitException
		 * @return	self
		 */
		public function add($file)
		{
			if(!is_array($file))
			{
				$file = func_get_args();
			}
			
			foreach($file as $item)
			{
				$this->run('git add', $item);
			}
			
			return $this;
		}
		
		
		
		/**
		 * Renames file(s).
		 * @param	string|string[]  from: array('from' => 'to', ...) || (from, to)
		 * @param	string|NULL
		 * @throws	Cz\Git\GitException
		 * @return	self
		 */
		public function rename($file, $to = NULL)
		{
			if(!is_array($file)) // rename(file, to);
			{
				$from[$file] = $to;
				$file = $from;
				unset($from);
			}
			
			foreach($file as $from => $to)
			{
				$this->run('git mv', $from, $to);
			}
			
			return $this;
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
			
			$this->run("git commit", $params, array(
				'-m' => $message,
			));
			return $this;
		}
		
		
		
		/**
		 * Exists changes?
		 * @return	bool
		 */
		public function isChanges()
		{
			$lastLine = exec('git status');
			
			return (strpos($lastLine, 'nothing to commit')) === FALSE; // FALSE => changes
		}
		
		
		
		/** Runs command.
		 * @param	string|array
		 * @return	void
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
		}
	}

