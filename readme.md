Git-PHP
============

Library for work with Git repository in PHP.

Usage
-----

``` php
<?php
	// new repo object
	$repo = new Cz\Git\GitRepository(__DIR__);
	
	// new file
	$filename = __DIR__ . '/first.txt';
	file_put_contents($filename, "Lorem ipsum\ndolor\nsit amet");
	
	// commit
	$repo->addFile($filename);
	$repo->commit('init commit');
```

[API documentation](http://api.iunas.cz/git-php/)


Installation
------------

[Download a latest package](https://github.com/czproject/git-php/releases) or use [Composer](http://getcomposer.org/):

```
composer require czproject/git-php
```

------------------------------

License: [New BSD License](license.md)
<br>Author: Jan Pecha, http://janpecha.iunas.cz/
