<?php

namespace Fantastique\Exceptions;

use Fantastique\Page;

/**
 * Exception thrown whenever there's some sort of issue while trying to render
 * a page.
 */
class RenderException extends Exception {
	public function __construct(string $message = 'Something went wrong with a render',
	                            Page $page, ?\Throwable $previous = null) {
		parent::__construct($message, 20, $previous);
	}
}
