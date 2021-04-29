<?php

	namespace CzProject\GitPhp;


	class GitRepository
	{
		/** @var  string */
		protected $repository;

		/** @var IRunner */
		protected $runner;


		/**
		 * @param  string
		 * @throws GitException
		 */
		public function __construct($repository, IRunner $runner = NULL)
		{
			if (basename($repository) === '.git') {
				$repository = dirname($repository);
			}

			$this->repository = realpath($repository);

			if ($this->repository === FALSE) {
				throw new GitException("Repository '$repository' not found.");
			}

			$this->runner = $runner !== NULL ? $runner : new Runners\CliRunner;
		}


		/**
		 * @return string
		 */
		public function getRepositoryPath()
		{
			return $this->repository;
		}


		/**
		 * Creates a tag.
		 * `git tag <name>`
		 * @param  string
		 * @param  array|NULL
		 * @throws GitException
		 * @return static
		 */
		public function createTag($name, $options = NULL)
		{
			$this->run('tag', $options, $name);
			return $this;
		}


		/**
		 * Removes tag.
		 * `git tag -d <name>`
		 * @param  string
		 * @throws GitException
		 * @return static
		 */
		public function removeTag($name)
		{
			$this->run('tag', [
				'-d' => $name,
			]);
			return $this;
		}


		/**
		 * Renames tag.
		 * `git tag <new> <old>`
		 * `git tag -d <old>`
		 * @param  string
		 * @param  string
		 * @throws GitException
		 * @return static
		 */
		public function renameTag($oldName, $newName)
		{
			// http://stackoverflow.com/a/1873932
			// create new as alias to old (`git tag NEW OLD`)
			$this->run('tag', $newName, $oldName);
			// delete old (`git tag -d OLD`)
			$this->removeTag($oldName);
			return $this;
		}


		/**
		 * Returns list of tags in repo.
		 * @return string[]|NULL  NULL => no tags
		 * @throws GitException
		 */
		public function getTags()
		{
			return $this->extractFromCommand(['tag'], 'trim');
		}


		/**
		 * Merges branches.
		 * `git merge <options> <name>`
		 * @param  string
		 * @param  array|NULL
		 * @throws GitException
		 * @return static
		 */
		public function merge($branch, $options = NULL)
		{
			$this->run('merge', $options, $branch);
			return $this;
		}


		/**
		 * Creates new branch.
		 * `git branch <name>`
		 * (optionaly) `git checkout <name>`
		 * @param  string
		 * @param  bool
		 * @throws GitException
		 * @return static
		 */
		public function createBranch($name, $checkout = FALSE)
		{
			// git branch $name
			$this->run('branch', $name);

			if ($checkout) {
				$this->checkout($name);
			}

			return $this;
		}


		/**
		 * Removes branch.
		 * `git branch -d <name>`
		 * @param  string
		 * @throws GitException
		 * @return static
		 */
		public function removeBranch($name)
		{
			$this->run('branch', [
				'-d' => $name,
			]);
			return $this;
		}


		/**
		 * Gets name of current branch
		 * `git branch` + magic
		 * @return string
		 * @throws GitException
		 */
		public function getCurrentBranchName()
		{
			try {
				$branch = $this->extractFromCommand(['branch', '-a'], function($value) {
					if (isset($value[0]) && $value[0] === '*') {
						return trim(substr($value, 1));
					}

					return FALSE;
				});

				if (is_array($branch)) {
					return $branch[0];
				}

			} catch (GitException $e) {
				// nothing
			}

			throw new GitException('Getting current branch name failed.');
		}


		/**
		 * Returns list of all (local & remote) branches in repo.
		 * @return string[]|NULL  NULL => no branches
		 * @throws GitException
		 */
		public function getBranches()
		{
			return $this->extractFromCommand(['branch', '-a'], function($value) {
				return trim(substr($value, 1));
			});
		}


		/**
		 * Returns list of remote branches in repo.
		 * @return string[]|NULL  NULL => no branches
		 * @throws GitException
		 */
		public function getRemoteBranches()
		{
			return $this->extractFromCommand(['branch', '-r'], function($value) {
				return trim(substr($value, 1));
			});
		}


		/**
		 * Returns list of local branches in repo.
		 * @return string[]|NULL  NULL => no branches
		 * @throws GitException
		 */
		public function getLocalBranches()
		{
			return $this->extractFromCommand(['branch'], function($value) {
				return trim(substr($value, 1));
			});
		}


		/**
		 * Checkout branch.
		 * `git checkout <branch>`
		 * @param  string
		 * @throws GitException
		 * @return static
		 */
		public function checkout($name)
		{
			$this->run('checkout', $name);
			return $this;
		}


		/**
		 * Removes file(s).
		 * `git rm <file>`
		 * @param  string|string[]
		 * @throws GitException
		 * @return static
		 */
		public function removeFile($file)
		{
			if (!is_array($file)) {
				$file = func_get_args();
			}

			foreach ($file as $item) {
				$this->run('rm', $item, '-r');
			}

			return $this;
		}


		/**
		 * Adds file(s).
		 * `git add <file>`
		 * @param  string|string[]
		 * @throws GitException
		 * @return static
		 */
		public function addFile($file)
		{
			if (!is_array($file)) {
				$file = func_get_args();
			}

			foreach ($file as $item) {
				// make sure the given item exists
				// this can be a file or an directory, git supports both
				$path = Helpers::isAbsolute($item) ? $item : ($this->getRepositoryPath() . DIRECTORY_SEPARATOR . $item);

				if (!file_exists($path)) {
					throw new GitException("The path at '$item' does not represent a valid file.");
				}

				$this->run('add', $item);
			}

			return $this;
		}


		/**
		 * Adds all created, modified & removed files.
		 * `git add --all`
		 * @throws GitException
		 * @return static
		 */
		public function addAllChanges()
		{
			$this->run('add', '--all');
			return $this;
		}


		/**
		 * Renames file(s).
		 * `git mv <file>`
		 * @param  string|string[]  from: array('from' => 'to', ...) || (from, to)
		 * @param  string|NULL
		 * @throws GitException
		 * @return static
		 */
		public function renameFile($file, $to = NULL)
		{
			if (!is_array($file)) { // rename(file, to);
				$file = [
					$file => $to,
				];
			}

			foreach ($file as $from => $to) {
				$this->run('mv', $from, $to);
			}

			return $this;
		}


		/**
		 * Commits changes
		 * `git commit <params> -m <message>`
		 * @param  string
		 * @param  string[]  param => value
		 * @throws GitException
		 * @return static
		 */
		public function commit($message, $params = NULL)
		{
			if (!is_array($params)) {
				$params = [];
			}

			$this->run('commit', $params, [
				'-m' => $message,
			]);
			return $this;
		}


		/**
		 * Returns last commit ID on current branch
		 * `git log --pretty=format:"%H" -n 1`
		 * @return string|NULL
		 * @throws GitException
		 */
		public function getLastCommitId()
		{
			$result = $this->run('log', '--pretty=format:"%H"', '-n', '1');
			$lastLine = $result->getOutputLastLine();

			if (is_string($lastLine) && preg_match('/^[0-9a-f]{40}$/i', $lastLine)) {
				return $lastLine;
			}

			return NULL;
		}


		/**
		 * Exists changes?
		 * `git status` + magic
		 * @return bool
		 * @throws GitException
		 */
		public function hasChanges()
		{
			// Make sure the `git status` gets a refreshed look at the working tree.
			$this->run('update-index', '-q', '--refresh');
			$result = $this->run('status', '--porcelain');
			return $result->hasOutput();
		}


		/**
		 * @deprecated
		 * @throws GitException
		 */
		public function isChanges()
		{
			return $this->hasChanges();
		}


		/**
		 * Pull changes from a remote
		 * @param  string|NULL
		 * @param  array|NULL
		 * @return static
		 * @throws GitException
		 */
		public function pull($remote = NULL, array $params = NULL)
		{
			if (!is_array($params)) {
				$params = [];
			}

			$this->run('pull', $remote, $params);
			return $this;
		}


		/**
		 * Push changes to a remote
		 * @param  string|NULL
		 * @param  array|NULL
		 * @return static
		 * @throws GitException
		 */
		public function push($remote = NULL, array $params = NULL)
		{
			if (!is_array($params)) {
				$params = [];
			}

			$this->run('push', $remote, $params);
			return $this;
		}


		/**
		 * Run fetch command to get latest branches
		 * @param  string|NULL
		 * @param  array|NULL
		 * @return static
		 * @throws GitException
		 */
		public function fetch($remote = NULL, array $params = NULL)
		{
			if (!is_array($params)) {
				$params = [];
			}

			$this->run('fetch', $remote, $params);
			return $this;
		}


		/**
		 * Adds new remote repository
		 * @param  string
		 * @param  string
		 * @param  array|NULL
		 * @return static
		 * @throws GitException
		 */
		public function addRemote($name, $url, array $params = NULL)
		{
			$this->run('remote', 'add', $params, $name, $url);
			return $this;
		}


		/**
		 * Renames remote repository
		 * @param  string
		 * @param  string
		 * @return static
		 * @throws GitException
		 */
		public function renameRemote($oldName, $newName)
		{
			$this->run('remote', 'rename', $oldName, $newName);
			return $this;
		}


		/**
		 * Removes remote repository
		 * @param  string
		 * @return static
		 * @throws GitException
		 */
		public function removeRemote($name)
		{
			$this->run('remote', 'remove', $name);
			return $this;
		}


		/**
		 * Changes remote repository URL
		 * @param  string
		 * @param  string
		 * @param  array|NULL
		 * @return static
		 * @throws GitException
		 */
		public function setRemoteUrl($name, $url, array $params = NULL)
		{
			$this->run('remote', 'set-url', $params, $name, $url);
			return $this;
		}


		/**
		 * @param  string|string[]
		 * @return string[]  returns output
		 * @throws GitException
		 */
		public function execute($cmd)
		{
			if (!is_array($cmd)) {
				$cmd = [$cmd];
			}

			$result = $this->run(...$cmd);
			return $result->getOutput();
		}


		/**
		 * @param  string
		 * @param  callback|NULL
		 * @return string[]|NULL
		 * @throws GitException
		 */
		protected function extractFromCommand(array $args, $filter = NULL)
		{
			$result = $this->run(...$args);
			$output = $result->getOutput();

			if ($filter !== NULL) {
				$newArray = [];

				foreach ($output as $line) {
					$value = $filter($line);

					if ($value === FALSE) {
						continue;
					}

					$newArray[] = $value;
				}

				$output = $newArray;
			}

			if (empty($output)) {
				return NULL;
			}

			return $output;
		}


		/**
		 * Runs command.
		 * @return RunnerResult
		 * @throws GitException
		 */
		protected function run(...$args)
		{
			$result = $this->runner->run($this->repository, $args);

			if (!$result->isOk()) {
				throw new GitException("Command '{$result->getCommand()}' failed (exit-code {$result->getExitCode()}).", $result->getExitCode(), NULL, $result);
			}

			return $result;
		}


		/**
		 * Returns commit message from specific commit
		 * `git log -1 --format={%s|%B} )--pretty=format:'%H' -n 1`
		 * @param  string  commit ID
		 * @param  bool    use %s instead of %B if TRUE
		 * @return string
		 * @throws GitException
		 */
		public function getCommitMessage($commit, $oneline = FALSE)
		{
			$result = $this->run('log', '-1', '--format=' . ($oneline ? '%s' : '%B'), $commit);
			return implode(PHP_EOL, $result->getOutput());
		}

		/**
		 * Returns commit date from specific commit
		 * `git log -1 --date=iso-strict`
		 * @param  string          commit ID (if empty last commit)
		 * @param  string          date format (eg. 'iso-strict' or 'format:'%Y-%m-%d %H:%M:%S'')
		 * @return \DateTime|NULL
		 * @throws GitException
		 */
		public function getCommitDate($commit = '', $dateFormat = 'iso-strict')
		{
			$result = $this->run('log', '-1', $commit, '--pretty="format:%cd"', '--date=' . $dateFormat);
			$lastLine = $result->getOutputLastLine();

			if ($lastLine === NULL) {
				return NULL;
			}

			try {
				return new \DateTime($lastLine);

			} catch (\Exception $e) {
				return null;
			}
		}


		/**
		 * Returns commit author from specific commit
		 * `git log -1 --format='%ae'`
		 * @param  string      commit ID (if empty last commit)
		 * @return string|NULL
		 * @throws GitException
		 */
		public function getCommitAuthor($commit = '')
		{
			$result = $this->run('log', '-1', $commit, '--format="%ae"');
			$lastLine = $result->getOutputLastLine();
			return $lastLine;
		}


		/**
		 * Returns array of commit metadata from specific commit
		 * `git show --raw <sha1>`
		 * @param  string  commit ID
		 * @return array
		 * @throws GitException
		 */
		public function getCommitData($commit)
		{
			$message = $this->getCommitMessage($commit);
			$subject = $this->getCommitMessage($commit, TRUE);

			$result = $this->run('show', '--raw', $commit);
			$data = [
				'commit' => $commit,
				'subject' => $subject,
				'message' => $message,
				'author' => NULL,
				'committer' => NULL,
				'date' => NULL,
			];

			// git show is a porcelain command and output format may changes
			// in future git release or custom config.
			foreach ($result->getOutput() as $index => $info) {
				if (preg_match('`Author: *(.*)`', $info, $author)) {
					$data['author'] = trim($author[1]);
				}

				if (preg_match('`Commit: *(.*)`', $info, $committer)) {
					$data['committer'] = trim($committer[1]);
				}

				if (preg_match('`Date: *(.*)`', $info, $date)) {
					$data['date'] = trim($date[1]);
				}
			}

			return $data;
		}
	}
