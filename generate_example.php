<?php
/**
 * generate_example.php
 * Generates the example website. This is itself an example of how to compose
 * your builder script for your own static website.
 *
 * @author Nathan Campos <nathan@innoveworkshop.com>
 */

require_once __DIR__ . '/vendor/autoload.php';

use \Fantastique\Page;

$page = (new Page('./example', './example/index.php'))->render();
$page = (new Page('./example', './example/about.php'))->render();
