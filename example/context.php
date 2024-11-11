<?php
$this->fill(array(
	'title' => 'Contextual',
	'description' => 'A page with context.'
));
?>
<html lang="en">
<head>
	<title><?= $this->title ?></title>
</head>
<body>
	<h1><?= $this->title ?></h1>
	<pre><code><?php print_r($this->context); ?></code></pre>
</body>
</html>
