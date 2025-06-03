<?php

declare(strict_types=1);

use Tester\Assert;
use CzProject\GitPhp\Git;
use CzProject\GitPhp\Tests\AssertRunner;

require __DIR__ . '/bootstrap.php';

test(function () {
	$runner = new AssertRunner(__DIR__);
	$git = new Git($runner);

	// last commit ID
	$runner->assert(
		['log', '--pretty=format:%H', '-n', '1'],
		[],
		['734713bc047d87bf7eac9674765ae793478c50d3']
	);

	// commit subject
	$runner->assert(
		['log', '-1', '734713bc047d87bf7eac9674765ae793478c50d3', '--format=%s'],
		[],
		['init commit', '', '']
	);

	// commit body
	$runner->assert(
		['log', '-1', '734713bc047d87bf7eac9674765ae793478c50d3', '--format=%b'],
		[],
		['', '', ''] // 3 empty lines
	);

	// author email
	$runner->assert(
		['log', '-1', '734713bc047d87bf7eac9674765ae793478c50d3', '--format=%ae'],
		[],
		['john@example.com']
	);

	// author name
	$runner->assert(
		['log', '-1', '734713bc047d87bf7eac9674765ae793478c50d3', '--format=%an'],
		[],
		['John Example']
	);

	// author date
	$runner->assert(
		['log', '-1', '734713bc047d87bf7eac9674765ae793478c50d3', '--pretty=format:%ad', '--date=iso-strict'],
		[],
		["2021-04-29T15:55:09+00:00"]
	);

	// committer email
	$runner->assert(
		['log', '-1', '734713bc047d87bf7eac9674765ae793478c50d3', '--format=%ce'],
		[],
		["john@committer.com"]
	);

	// committer name
	$runner->assert(
		['log', '-1', '734713bc047d87bf7eac9674765ae793478c50d3', '--format=%cn'],
		[],
		["John Committer"]
	);

	// committter date
	$runner->assert(
		['log', '-1', '734713bc047d87bf7eac9674765ae793478c50d3', '--pretty=format:%cd', '--date=iso-strict'],
		[],
		['2021-04-29T17:55:09+02:00']
	);

	$timestamp = \DateTimeImmutable::createFromFormat(\DateTime::ATOM, '2021-04-29T16:55:09+01:00')->getTimestamp();

	$repo = $git->open(__DIR__);
	$commit = $repo->getLastCommit();
	Assert::same('734713bc047d87bf7eac9674765ae793478c50d3', $commit->getId()->toString());
	Assert::same('init commit', $commit->getSubject());
	Assert::null($commit->getBody());
	Assert::same($timestamp, $commit->getDate()->getTimestamp());

	Assert::same('john@example.com', $commit->getAuthorEmail());
	Assert::same('John Example', $commit->getAuthorName());
	Assert::same($timestamp, $commit->getAuthorDate()->getTimestamp());

	Assert::same('john@committer.com', $commit->getCommitterEmail());
	Assert::same('John Committer', $commit->getCommitterName());
	Assert::same($timestamp, $commit->getCommitterDate()->getTimestamp());
});


test(function () {
	$runner = new AssertRunner(__DIR__);
	$git = new Git($runner);

	// commit subject
	$runner->assert(
		['log', '-1', '734713bc047d87bf7eac9674765ae793478c50d3', '--format=%s'],
		[],
		['init commit', '', '']
	);

	// commit body
	$runner->assert(
		['log', '-1', '734713bc047d87bf7eac9674765ae793478c50d3', '--format=%b'],
		[],
		['first line', 'second line', '', ''] // + 2 empty lines
	);

	// author email
	$runner->assert(
		['log', '-1', '734713bc047d87bf7eac9674765ae793478c50d3', '--format=%ae'],
		[],
		['john@example.com']
	);

	// author name
	$runner->assert(
		['log', '-1', '734713bc047d87bf7eac9674765ae793478c50d3', '--format=%an'],
		[],
		['John Example']
	);

	// author date
	$runner->assert(
		['log', '-1', '734713bc047d87bf7eac9674765ae793478c50d3', '--pretty=format:%ad', '--date=iso-strict'],
		[],
		["2021-04-29T15:55:09+00:00"]
	);

	// committer email
	$runner->assert(
		['log', '-1', '734713bc047d87bf7eac9674765ae793478c50d3', '--format=%ce'],
		[],
		["john@committer.com"]
	);

	// committer name
	$runner->assert(
		['log', '-1', '734713bc047d87bf7eac9674765ae793478c50d3', '--format=%cn'],
		[],
		["John Committer"]
	);

	// committter date
	$runner->assert(
		['log', '-1', '734713bc047d87bf7eac9674765ae793478c50d3', '--pretty=format:%cd', '--date=iso-strict'],
		[],
		['2021-04-29T17:55:09+02:00']
	);

	$timestamp = \DateTimeImmutable::createFromFormat(\DateTime::ATOM, '2021-04-29T16:55:09+01:00')->getTimestamp();

	$repo = $git->open(__DIR__);
	$commit = $repo->getCommit('734713bc047d87bf7eac9674765ae793478c50d3');
	Assert::same('734713bc047d87bf7eac9674765ae793478c50d3', $commit->getId()->toString());
	Assert::same('init commit', $commit->getSubject());
	Assert::same("first line\nsecond line", $commit->getBody());
	Assert::same($timestamp, $commit->getDate()->getTimestamp());

	Assert::same('john@example.com', $commit->getAuthorEmail());
	Assert::same('John Example', $commit->getAuthorName());
	Assert::same($timestamp, $commit->getAuthorDate()->getTimestamp());

	Assert::same('john@committer.com', $commit->getCommitterEmail());
	Assert::same('John Committer', $commit->getCommitterName());
	Assert::same($timestamp, $commit->getCommitterDate()->getTimestamp());
});
