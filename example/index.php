<?php
$this->fill(array(
	'title' => 'Main page',
	'description' => 'An example of a main page.'
));
?>
<html lang="en">
<head>
	<title><?= $this->title ?></title>
	<link rel="stylesheet" href="css/style.css">
</head>
<body>
	<h1>It works!</h1>
	<p><?= $this->title ?></p>
</body>
</html>
