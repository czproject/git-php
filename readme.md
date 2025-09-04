Git-PHP
=======

[![Build Status](https://github.com/czproject/git-php/workflows/Build/badge.svg)](https://github.com/czproject/git-php/actions)
[![Downloads this Month](https://img.shields.io/packagist/dm/czproject/git-php.svg)](https://packagist.org/packages/czproject/git-php)
[![Latest Stable Version](https://poser.pugx.org/czproject/git-php/v/stable)](https://github.com/czproject/git-php/releases)
[![License](https://img.shields.io/badge/license-New%20BSD-blue.svg)](https://github.com/czproject/git-php/blob/master/license.md)


Library for work with Git repository in PHP.

<a href="https://www.janpecha.cz/donate/git-php/"><img src="https://buymecoffee.intm.org/img/donate-banner.v1.svg" alt="Donate" height="100"></a>

> [!TIP]
> You can use [GitHub Sponsors](https://github.com/sponsors/janpecha), [Stripe.com](https://donate.stripe.com/7sIcO2a9maTSg2A9AA), [Thanks.dev](https://thanks.dev/u/gh/czproject) or [other](https://www.janpecha.cz/donate/git-php/) method.


Installation
------------

[Download a latest package](https://github.com/czproject/git-php/releases) or use [Composer](http://getcomposer.org/):

```
composer require czproject/git-php
```

Library requires PHP 8.0 or later and `git` client (path to Git must be in system variable `PATH`).

Git installers:

* for Linux - https://git-scm.com/download/linux
* for Windows - https://git-scm.com/download/win
* for others - https://git-scm.com/downloads


Usage
-----

``` php
$git = new CzProject\GitPhp\Git;
// create repo object
$repo = $git->open('/path/to/repo');

// create a new file in repo
$filename = $repo->getRepositoryPath() . '/readme.txt';
file_put_contents($filename, "Lorem ipsum
	dolor
	sit amet
");

// commit
$repo->addFile($filename);
$repo->commit('init commit');
```


Initialization of empty repository
----------------------------------

``` php
$repo = $git->init('/path/to/repo-directory');
```

With parameters:

``` php
$repo = $git->init('/path/to/repo-directory', [
	'--bare', // creates bare repo
]);
```


Cloning of repository
---------------------

``` php
// Cloning of repository into subdirectory 'git-php' in current working directory
$repo = $git->cloneRepository('https://github.com/czproject/git-php.git');

// Cloning of repository into own directory
$repo = $git->cloneRepository('https://github.com/czproject/git-php.git', '/path/to/my/subdir');
```


Basic operations
----------------

``` php
$repo->hasChanges();    // returns boolean
$repo->commit('commit message');
$repo->merge('branch-name');
$repo->checkout('master');

$repo->getRepositoryPath();

// adds files into commit
$repo->addFile('file.txt');
$repo->addFile('file1.txt', 'file2.txt');
$repo->addFile(['file3.txt', 'file4.txt']);

// renames files in repository
$repo->renameFile('old.txt', 'new.txt');
$repo->renameFile([
    'old1.txt' => 'new1.txt',
    'old2.txt' => 'new2.txt',
]);

// removes files from repository
$repo->removeFile('file.txt');
$repo->removeFile('file1.txt', 'file2.txt');
$repo->removeFile(['file3.txt', 'file4.txt']);

// adds all changes in repository
$repo->addAllChanges();
```



Branches
--------

``` php
// gets list of all repository branches (remotes & locals)
$repo->getBranches();

// gets list of all local branches
$repo->getLocalBranches();

// gets name of current branch
$repo->getCurrentBranchName();

// creates new branch
$repo->createBranch('new-branch');

// creates new branch and checkout
$repo->createBranch('patch-1', TRUE);

// removes branch
$repo->removeBranch('branch-name');
```


Tags
----

``` php
// gets list of all tags in repository
$repo->getTags();

// creates new tag
$repo->createTag('v1.0.0');
$repo->createTag('v1.0.0', $options);
$repo->createTag('v1.0.0', [
	'-m' => 'message',
]);

// renames tag
$repo->renameTag('old-tag-name', 'new-tag-name');

// removes tag
$repo->removeTag('tag-name');
```


History
-------

``` php
// returns last commit ID on current branch
$commitId = $repo->getLastCommitId();
$commitId->getId(); // or (string) $commitId

// returns commit data
$commit = $repo->getCommit('734713bc047d87bf7eac9674765ae793478c50d3');
$commit->getId(); // instance of CommitId
$commit->getSubject();
$commit->getBody();
$commit->getAuthorName();
$commit->getAuthorEmail();
$commit->getAuthorDate();
$commit->getCommitterName();
$commit->getCommitterEmail();
$commit->getCommitterDate();
$commit->getDate();

// returns commit data of last commit on current branch
$commit = $repo->getLastCommit();
```


Remotes
-------

``` php
// pulls changes from remote
$repo->pull('remote-name', ['--options']);
$repo->pull('origin');

// pushs changes to remote
$repo->push('remote-name', ['--options']);
$repo->push('origin');
$repo->push(['origin', 'master'], ['-u']);

// fetchs changes from remote
$repo->fetch('remote-name', ['--options']);
$repo->fetch('origin');
$repo->fetch(['origin', 'master']);

// adds remote repository
$repo->addRemote('remote-name', 'repository-url', ['--options']);
$repo->addRemote('origin', 'git@github.com:czproject/git-php.git');

// renames remote
$repo->renameRemote('old-remote-name', 'new-remote-name');
$repo->renameRemote('origin', 'upstream');

// removes remote
$repo->removeRemote('remote-name');
$repo->removeRemote('origin');

// changes remote URL
$repo->setRemoteUrl('remote-name', 'new-repository-url');
$repo->setRemoteUrl('upstream', 'https://github.com/czproject/git-php.git');
```

**Troubleshooting - How to provide username and password for commands**

1) use SSH instead of HTTPS - https://stackoverflow.com/a/8588786
2) store credentials to *Git Credential Storage*
	* http://www.tilcode.com/push-github-without-entering-username-password-windows-git-bash/
	* https://help.github.com/articles/caching-your-github-password-in-git/
	* https://git-scm.com/book/en/v2/Git-Tools-Credential-Storage
3) insert user and password into remote URL - https://stackoverflow.com/a/16381160
	* `git remote add origin https://user:password@server/path/repo.git`
4) for `push()` you can use `--repo` argument - https://stackoverflow.com/a/12193555
	* `$git->push(NULL, ['--repo' => 'https://user:password@server/path/repo.git']);`


Other commands
--------------

For running other commands you can use `execute` method:

```php
$output = $repo->execute('command');
$output = $repo->execute('command', 'with', 'parameters');

// example:
$repo->execute('remote', 'set-branches', $originName, $branches);
```


Custom methods
--------------

You can create custom methods. For example:

``` php
class OwnGit extends \CzProject\GitPhp\Git
{
	public function open($directory)
	{
		return new OwnGitRepository($directory, $this->runner);
	}
}

class OwnGitRepository extends \CzProject\GitPhp\GitRepository
{
	public function setRemoteBranches($name, array $branches)
	{
		$this->run('remote', 'set-branches', $name, $branches);
		return $this;
	}
}


$git = new OwnGit;
$repo = $git->open('/path/to/repo');
$repo->addRemote('origin', 'repository-url');
$repo->setRemoteBranches('origin', [
	'branch-1',
	'branch-2',
]);
```

------------------------------

License: [New BSD License](license.md)
<br>Author: Jan Pecha, https://www.janpecha.cz/
