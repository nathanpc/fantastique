<?php
/**
 * generate_example.php
 * Generates the example website. This is itself an example of how to compose
 * your builder script for your own static website.
 *
 * @author Nathan Campos <nathan@innoveworkshop.com>
 */

require_once __DIR__ . '/vendor/autoload.php';

use \Fantastique\Builder;

// Get our builder helper.
$builder = new Builder(__DIR__ . '/example', __DIR__ . '/build');

// Render the example folder.
$builder->render_folder(__DIR__ . '/example');
