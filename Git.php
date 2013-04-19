<?php
	/** Default Implementation of IGit Interface
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 */
	
	namespace Cz\Git;
	
	use Cz\Git\GitException;
	
	class Git implements IGit
	{
		/**
		 * @param	string
		 * @return	$this
		 */
		public function tag($name)
		{
			$this->run("git tag", $name);
			return $this;
		}
		
		
		
		/**
		 * @param	string
		 * @param	string[]|string
		 * @return	$this
		 */
		public function merge($branch, $options = NULL)
		{
			$this->run("git merge", $options, $branch);
			return $this;
		}
		
		
		
		/**
		 * @param	string
		 * @param	bool
		 * @return	$this
		 */
		public function branchCreate($name, $checkout = FALSE)
		{
			// git branch $name
			$this->run("git branch", $name);
			
			if($checkout)
			{
				$this->checkout($name);
			}
			
			return $this;
		}
		
		
		
		/**
		 * @param	string
		 * @return	$this
		 */
		public function branchRemove($name)
		{
			$this->run("git branch", array(
				'-d' => $name,
			));
			return $this;
		}
		
		
		
		/**
		 * Gets current branch name
		 * @return	string
		 * @throws	GitException
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
			
			throw new GitException('Pokus o ziskani jmena aktualni vetvi selhal.');
		}
		
		
		
		/**
		 * @param	string
		 * @return	$this
		 */
		public function checkout($name)
		{
			$this->run("git checkout", $name);
			return $this;
		}
		
		
		
		/**
		 * @param	string
		 * @return	$this
		 */
		public function remove($file)
		{
			$this->run("git rm", $file, '-r');
			return $this;
		}
		
		
		
		/**
		 * @param	string
		 * @return	$this
		 */
		public function add($file)
		{
			$this->run("git add", $file);
			return $this;
		}
		
		
		
		/**
		 * @param	string
		 * @param	string|string[]|NULL
		 * @return	$this
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
		 * @return	bool
		 */
		public function isChanges()
		{
			$lastLine = exec('git status');
			
			return (strpos($lastLine, 'nothing to commit')) === FALSE; // FALSE => changes
		}
		
		
		
		/**
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
				throw new GitException("Prikaz '$cmd' selhal.");
			}
		}
	}

