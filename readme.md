Git-PHP
-------

Library for work with Git from PHP.

``` php
<?php
	$git = new Cz\Git\Git;
	$filename = __DIR__ . '/my-file.txt';
	file_put_contents($filename, "Lorem ipsum\ndolor\nsit amet");
	
	if($git->isChanges())
	{
		$git->add($filename)
			->commit('Added a file.');
	}
```

