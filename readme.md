Git-PHP
============

Library for work with Git repository from PHP.

Usage
-----

``` php
<?php
	$repo = new Cz\Git\GitRepository(__DIR__);
	$filename = __DIR__ . '/my-file.txt';
	file_put_contents($filename, "Lorem ipsum\ndolor\nsit amet");
	
	if($repo->isChanges())
	{
		$repo->addFile($filename)
			->commit('Added a file.');
	}
```


Installation
------------

[Download a latest package](https://github.com/czproject/git-php/releases) or use [Composer](http://getcomposer.org/):

```
composer require czproject/git-php
```

------------------------------

License: [New BSD License](license.md)
<br>Author: Jan Pecha, http://janpecha.iunas.cz/
