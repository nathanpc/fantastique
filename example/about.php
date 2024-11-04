<?php
$this->fill(array(
	'title' => 'About',
	'description' => 'About this project.'
));
?>
<html lang="en">
<head>
	<title><?= $this->title ?></title>
</head>
<body>
	<h1><?= $this->title ?></h1>
	<p>An unopinionated static website generator for PHP.</p>

	<h2>Motivation</h2>
	<p>The motivation for building this static site generator was simple. PHP is one of
		the most versatile ways of templating and quickly composing websites, but most
		of the time we only need it for simple things that can be static.</p>

	<p>This tiny module aims at creating a simple way to use PHP to create static
		websites while maintaining the greatest amount of flexibility possible.</p>

	<h2>License</h2>
	<p>This application is free software; you may redistribute and/or modify it under the
		terms of the <a href="https://www.mozilla.org/en-US/MPL/2.0/">Mozilla Public License 2.0</a>.</p>
</body>
</html>
