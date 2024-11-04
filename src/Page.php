<?php

namespace Fantastique;

use Fantastique\Exceptions\PathException;

/**
 * The main abstraction of a static page to be generated.
 */
class Page {
	protected ?string $title;
	protected ?string $description;
	protected string $path;
	protected string $filename;
	protected string $source;
	public array $context;

	/**
	 * Sets up a page for static generation using a source file.
	 *
	 * @param string $base_path Base path for the static structure.
	 * @param string $source    Path to source file for this page.
	 *
	 * @throws PathException if the base path and the file path are unrelated.
	 */
	public function __construct(string $base_path, string $source) {
		$this->source = realpath($source);
		$this->title = null;
		$this->description = null;
		$this->filename = 'index.html';
		$this->context = array();

		// Build up the path.
		$path_parts = pathinfo($this->relpath($base_path, $source));
		if ($path_parts['dirname'] === '/')
			$path_parts['dirname'] = '';
		$this->path = "{$path_parts['dirname']}/{$path_parts['filename']}";
	}

	/**
	 * Fills up properties from the side of the page's source.
	 *
	 * @param array $props Properties of the object to be assigned values.
	 */
	public function fill(array $props): void {
		foreach ($props as $prop => $value) {
			$this->$prop = $value;
		}
	}

	/**
	 * Renders the file to its final destination.
	 *
	 * @return Page Ourselves for composability reasons.
	 */
	public function render(): Page {
		// Render the template out to a string.
		ob_start();
		include_once($this->source);
		$content = ob_get_contents();
		ob_end_clean();

		// TODO: Write out to the file.
		print_r($content);

		return $this;
	}

	/**
	 * Builds a relative path based on a base path and a file path.
	 *
	 * @param string $base_path Relative path base/root.
	 * @param string $file_path Path to a file to be relativized.
	 *
	 * @return string Path to the file relative to the base path.
	 *
	 * @throws PathException if the base path and the file path are unrelated.
	 */
	protected function relpath(string $base_path, string $file_path): string {
		$base_path = realpath($base_path);
		$file_path = realpath($file_path);

		// Check if the base path is actually related to the file path.
		if (!str_starts_with($file_path, $base_path))
			throw new PathException("Base path and file path are unrelated");

		// Get the relative path.
		$relpath = substr($file_path, strlen($base_path));

		// Handle the special case of the index.
		if (basename($file_path) === 'index.php')
			return dirname($relpath);

		return $relpath;
	}
}
