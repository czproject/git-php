Git-PHP
=======

Library for work with Git repository in PHP.

Usage
-----

``` php
<?php
	// create repo object
	$repo = new Cz\Git\GitRepository('/path/to/repo');

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
<?php
$repo = GitRepository::init('/path/to/repo-directory');
```

With parameters:

``` php
<?php
$repo = GitRepository::init('/path/to/repo-directory', array(
	'--bare', // creates bare repo
));
```


Cloning of repository
---------------------

``` php
<?php
// Cloning of repository into subdirectory 'git-php' in current working directory
$repo = GitRepository::cloneRepository('https://github.com/czproject/git-php.git');

// Cloning of repository into own directory
$repo = GitRepository::cloneRepository('https://github.com/czproject/git-php.git', '/path/to/my/subdir');
```


Basic operations
----------------

``` php
<?php
$repo->hasChanges();    // returns boolean
$repo->commit('commit message');
$repo->merge('branch-name');
$repo->checkout('master');
$repo->pull('origin');
$repo->push('origin');
$repo->fetch('origin');

$repo->getRepositoryPath();

// adds files into commit
$repo->addFile('file.txt');
$repo->addFile('file1.txt', 'file2.txt');
$repo->addFile(array('file3.txt', 'file4.txt'));

// renames files in repository
$repo->renameFile('old.txt', 'new.txt');
$repo->renameFile(array(
    'old1.txt' => 'new1.txt',
    'old2.txt' => 'new2.txt',
));

// removes files from repository
$repo->removeFile('file.txt');
$repo->removeFile('file1.txt', 'file2.txt');
$repo->removeFile(array('file3.txt', 'file4.txt'));

// adds all changes in repository
$repo->addAllChanges();
```



Branches
--------

``` php
<?php
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
<?php
// gets list of all tags in repository
$repo->getTags();

// creates new tag
$repo->createTag('v1.0.0');

// renames tag
$repo->renameTag('old-tag-name', 'new-tag-name');

// removes tag
$repo->removeTag('tag-name');
```


Installation
------------

[Download a latest package](https://github.com/czproject/git-php/releases) or use [Composer](http://getcomposer.org/):

```
composer require czproject/git-php
```

Library requires PHP 5.4 or later.

------------------------------

License: [New BSD License](license.md)
<br>Author: Jan Pecha, https://www.janpecha.cz/
