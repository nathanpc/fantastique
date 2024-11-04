<?php

namespace Fantastique\Exceptions;

/**
 * Exception thrown whenever there's some sort of issue with a path.
 */
class PathException extends Exception {
	public function __construct(string $message = 'Something went wrong with a path somewhere',
	                            ?\Throwable $previous = null) {
		parent::__construct($message, 10, $previous);
	}
}
